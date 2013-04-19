<?php
$eversion = "0.0.1";

function win_time($timestr)
{
	return substr($timestr, 0, 4) . "-" . substr($timestr, 4, 2) . "-" . substr($timestr, 6, 2) . " " . substr($timestr, 8, 2) . ":" . substr($timestr, 10, 2) . ":" . substr($timestr, 12, 2);
}
$runTimeEvent = date('U');
$batchsize = 1000;

$snorm = array
(
	"Template",
	"InsertionStrings",
	"Category",
	"EventCode",
	"LogFile",
	"SourceName",
	"Type",
	"User"
);

// Determines if this is the first run and creates tables if it is
try
{
	$dbh = new PDO("sqlite:" . $config['module']['eventLog']['path'] . "/eventLog.sqlite3");
	$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	$dbh->exec("PRAGMA journal_mode = MEMORY; PRAGMA temp_store = MEMORY; PRAGMA synchronous = OFF");
}
catch(PDOException $e)
{
	echo $e->getMessage();
};

$query = "
	SELECT
		COUNT(*) as 'Count'
	FROM agent_module
	WHERE
		moduleName = 'eventLog';";
$result   = $avarice_dbh->query($query)->fetch();
$firstRun = $result['Count'];

//$firstRun = 1;

if ($firstRun == 0)
{
	$query = "
		CREATE TABLE eventLog_logFiles
		(
			createdDate TEXT,
			modifiedDate TEXT,
			logFile TEXT UNIQUE,
			lastEventID NUMERIC
		);
		CREATE TABLE eventLog_runLog
		(
			startTime TEXT,
			endTime TEXT,
			status TEXT,
			eventLogs TEXT,
			eventCount INT
		);
		CREATE TABLE eventLog_upload
		(
			startTime TEXT,
			endTime TEXT,
			lastEventID INTEGER
		);
		CREATE TABLE mf_messageFile
		(
			messageFileID INTEGER PRIMARY KEY,
			eventLog TEXT,
			source TEXT,
			file TEXT,
			fileVersion TEXT,
			productVersion TEXT,
			fileCreated TEXT,
			fileModified TEXT,
			dateCreated TEXT,
			fileHash TEXT
		);
		CREATE TABLE mf_message
		(
			messageID INTEGER PRIMARY KEY,
			messageFileID INTEGER,
			identifier INTEGER,
			messageTemplate TEXT,
			jenkins1 TEXT,
			jenkins2 TEXT
		);
		CREATE TABLE events
		(
			pkID INTEGER PRIMARY KEY,
			ComputerName TEXT,
			RecordNumber NUMERIC,
			TimeWritten TEXT,";
	foreach ($snorm as $value)
	{
		$dbh->exec("CREATE TABLE " . $value . " (pkID INTEGER PRIMARY KEY, " . $value . " TEXT UNIQUE)");
		$query .= "
			" . 	$value . "ID INT,";
	}
	$query = substr($query, 0, -1) . ");";
	$dbh->exec($query);
}

// Get Message Files and Templates
include_once($config['agentDirectory']['path'] . "include/registry.php");

$regarray = Win32RegistryIterator($o_Win32Registry = new COM('winmgmts://./root/default:StdRegProv'), HKEY_LOCAL_MACHINE, 'SYSTEM\\CurrentControlSet\\services\\eventlog');

$messageFiles = array();
$x = 0;
foreach ($regarray as $log => $sources)
{
	foreach ($sources as $source => $keys)
	{
		if
		(
			(count($keys) == 2 and isset($keys['type']) and isset($keys['value']))
			or
			(!is_array($keys))
		)
		{
			//Do nothing
		}
		else
		{
			foreach ($keys as $key => $data)
			{
				if (substr($key, -11) == 'MessageFile' and !empty($data['value']))
				{
					if (!isset($messageFiles[$log]))
					{
						$messageFiles[$log] = array();
					}
					if (!isset($messageFiles[$log][$source]))
					{
						$messageFiles[$log][$source] = array();
					}
					$messageFiles[$log][$source][] = $data['value'];
				}
			}
		}
	}
}


$objShell = new COM("Shell.Application");
foreach($messageFiles as $el => $sources)
{
	foreach($sources as $source => $files)
	{
		foreach ($files as $filel)
		{
			$filelist = explode(";", $filel);
			foreach ($filelist as $file)
			{
				unset($output);
				$file = str_ireplace(array("%systemroot%", "%programfiles%"), array(getenv('SYSTEMROOT'), getenv('PROGRAMFILES')), $file);
				if (!is_file($file) and !is_file(getenv('SYSTEMROOT') . "\\system32\\" . $file))
				{
					continue;
				}
				else if (!is_file($file))
				{
					$file = getenv('SYSTEMROOT') . "\\system32\\" . $file;
				}
				// Use this info to see if file already exists in DB
				$mf_filename = substr(strrchr($file, "\\"), 1);
				$objFolder = $objShell->Namespace(substr($file, 0, strrpos($file, "\\")));
				$fileVersion = $objFolder->GetDetailsOf($objFolder->ParseName($mf_filename), 156);
				$productVersion = $objFolder->GetDetailsOf($objFolder->ParseName($mf_filename), 271);
				$fileModified = date('Y-m-d H:i:s', strtotime($objFolder->GetDetailsOf($objFolder->ParseName($mf_filename), 3)));
				$fileCreated = date('Y-m-d H:i:s', strtotime($objFolder->GetDetailsOf($objFolder->ParseName($mf_filename), 4)));
				$file_hash = hash_file("md5", $file);
				$query = "
					select
						messageFileID
					from
						mf_messageFile
					where
						eventLog = '" . $el . "'
						and source = '" . $source . "'
						and file = '" . $mf_filename . "'
						and fileVersion = '" . $fileVersion . "'
						and productVersion = '" . $productVersion . "'
						and fileModified = '" . $fileModified . "'
						and fileHash = '" . $file_hash . "';";
				$result = $dbh->query($query)->fetch();
				// if file !exists use wrcinfo.exe to get messages
				if (empty($result['messageFileID']))
				{
					$insertQuery = "
						insert into mf_messageFile
							(eventLog, source, file, fileVersion, productVersion, fileCreated, fileModified, dateCreated, fileHash)
						values
							('" . $el . "','" . $source . "','" . $mf_filename . "','" . $fileVersion . "','" . $productVersion . "','" . $fileCreated . "','" . $fileModified . "','" . date('Y-m-d H:i:s', $runTimeEvent) . "','" . $file_hash . "');";
					$dbh->exec($insertQuery);
					$result = $dbh->query($query)->fetch();
					exec("wrcinfo.exe \"" . $file . "\"", $output);
					$templateLineIDs = array();
					foreach ($output as $k => $v)
					{
						if (stripos($v, "Msg-Template:") !== false)
						{
							$templateLineIDs[] = $k;
						}
					}
					foreach ($templateLineIDs as $templateLineID)
					{
						#messageFileID, identifier, messageTemplate, jenkins1, jenkins2
						$identifier = substr($output[$templateLineID - 1], 8);
						$messageTemplate = substr($output[$templateLineID], 14);
						$x = 1;
						while (substr($output[$templateLineID + $x], 0, 14) != "Template-Hash:")
						{
							$messageTemplate += "
" . $output[$templateLineID + $x];
							$x++;
						}
						$messageTemplate = str_replace("'", "''", $messageTemplate);
						$jenks = substr($output[$templateLineID + $x], 15);
						list($jenkins1, $jenkins2) = explode(",", $jenks);
						$insertQuery = "
							insert into mf_message
								(messageFileID, identifier, messageTemplate, jenkins1, jenkins2)
							values
								('" . $result['messageFileID'] . "', '" . $identifier . "', '" . $messageTemplate . "', '" . $jenkins1 . "', '" . $jenkins2 . "');";
						$dbh->exec($insertQuery);
					}
				}
			}
		}
	}
}

/*
// Make WMI connection
$objWMIService = new COM("winmgmts:{impersonationLevel=impersonate,authenticationLevel=pktPrivacy,(Security)}!//.\\root\\cimv2");

// Gather list of EventLog Files
$logFileDetails = $objWMIService->ExecQuery("Select * from Win32_NTEventLogFile",'WQL',48);
$logfiles_array = array();
foreach ($logFileDetails as $logFileDetail)
{
	$logfiles_array[] = $logFileDetail->LogFileName;
}

$total = 0;

$emptyvariant = $objWMIService->ExecQuery("Select * from Win32_NTLogEvent WHERE RecordNumber = 'string'",'WQL',48);

foreach ($logfiles_array as $logfilename)
{
	$x = 0;
	$query = "
		SELECT
			*
		FROM eventLog_logFiles
		WHERE
			logFile = '" . $logfilename . "';";
	try
	{
		$result = $dbh->query($query)->fetch();
	}
	catch(PDOException $e)
	{
		print $e->getMessage();
	};
	if (empty($result['lastEventID']))
	{
		$result['lastEventID'] = 0;
	}
	$largestEvent = $result['lastEventID'] + 1000000;
	$colItems = $objWMIService->ExecQuery("Select * from Win32_NTLogEvent WHERE LogFile = '" . $logfilename . "' AND RecordNumber > " . $result['lastEventID'] . " AND RecordNumber < " . $largestEvent,'WQL',48);
	if ($colItems != $emptyvariant)
	{
		$query = "BEGIN TRANSACTION; ";
		foreach ($colItems as $objItem)
		{
			foreach ($snorm as $value)
			{
				if ($x == 0)
				{
					${"norm_query_" . $value} = "BEGIN TRANSACTION; ";
				}
				if (!in_array($value, array("InsertionStrings", "Template")))
				{
					${"norm_query_" . $value} .= "
					INSERT OR IGNORE INTO " . $value . " (" . $value . ") VALUES ('" . $objItem->$value . "'); ";
				}
				else if ($value == "InsertionStrings")
				{
					${"norm_query_" . $value} .= "
					INSERT OR IGNORE INTO " . $value . " (" . str_replace("'", "''", $value) . ") VALUES ('";
					$insertionStrings = array();
					if ($objItem->$value != NULL)
					{
						foreach ($objItem->$value as $oiv)
						{
							$insertionStrings[] = $oiv;
						}
					}
					${"norm_query_" . $value} .= str_replace("'", "''", implode(",", $insertionStrings)) . "'); ";
				}
				else if ($value == "Template")
				{
					$template = $objItem->Message;
					$template = str_replace(array("%", "\r", "\n", "\t"), array("%%", "%r", "%n", "%t"), $template);
					$y = 0;
					if ($objItem->InsertionStrings != NULL)
					{
						foreach ($objItem->InsertionStrings as $is)
						{
							$template = str_replace($is, '%' . $y, $template);
							$y++;
						}
					}
					${"norm_query_" . $value} .= "
					INSERT OR IGNORE INTO " . $value . " (" . $value . ") VALUES ('" . str_replace("'", "''", $template) . "'); ";
				}
				if ($x >= $batchsize)
				{
					${"norm_query_" . $value} .= " COMMIT;";
					$dbh->exec(${"norm_query_" . $value});
					${"norm_query_" . $value} = "BEGIN TRANSACTION;";
				}
			}
			$query .= "INSERT INTO Events (ComputerName, RecordNumber, TimeWritten, TemplateID, InsertionStringsID, CategoryID, EventCodeID, LogFileID, SourceNameID, TypeID, UserID) VALUES
					(
						'" . $objItem->ComputerName . "',
						'" . $objItem->RecordNumber . "',
						'" . win_time($objItem->TimeWritten) . "',";
			foreach ($snorm as $value)
			{
				$query .= "
						(
							SELECT
								pkID
							FROM
								" . $value . "
							WHERE
								" . $value . " = '";
				if (!in_array($value, array("InsertionStrings", "Template")))
				{
					$query .= str_replace("'", "''", $objItem->$value);
				}
				else if ($value == "InsertionStrings")
				{
					$query .= str_replace("'", "''", implode(",", $insertionStrings));
				}
				else if ($value == "Template")
				{
					$query .= str_replace("'", "''", $template);
				}
				$query .= "'
						),";
			};
			$query = substr($query, 0, -1) . "
					); ";
			if ($x < $batchsize)
			{
				$x++;
			}
			else
			{
				$total += $x;
				$x = 0;
				//print $query . " COMMIT;\n\n";
				$dbh->exec($query . " COMMIT;");
				$query = "
					BEGIN TRANSACTION; ";
			};
		};
		foreach ($snorm as $key => $value)
		{
			if (!empty(${"norm_query_" . $value}))
			{
				$dbh->exec(${"norm_query_" . $value} . " COMMIT;");
			};
		};
		$dbh->exec($query . " COMMIT;");
		$total += $x;
		$query = "
			SELECT
				IFNULL(MAX(RecordNumber), 0) AS 'RecordNumber'
			FROM Events
			WHERE
				LogFileID = (
					SELECT
						pkID
					FROM LogFile
					WHERE
						LogFile = '" . $logfilename . "'
				);";
		$result = $dbh->query($query)->fetch();
		if ($result['RecordNumber'] == 4294967295)
		{
			$result['RecordNumber'] = 0;
		};
		$query = "
			INSERT OR IGNORE INTO eventLog_logFiles
				(createdDate, logFile)
			VALUES
				('" . date('Y-m-d H:i:s', $runTimeEpoch) . "', '" . $logfilename . "');
			UPDATE eventLog_logFiles
			SET
				modifiedDate = '" . date('Y-m-d H:i:s', $runTimeEvent) . "',
				lastEventID = " . $result['RecordNumber'] . "
			WHERE
				logFile = '" . $logfilename . "';
		";
		$dbh->exec($query);
	};
};
$query = "
	INSERT INTO eventLog_runLog
		(startTime, endTime, status, eventLogs, eventCount)
	VALUES
		('" . date('Y-m-d H:i:s', $runTimeEvent) . "', '" . date('Y-m-d H:i:s') . "', 'success', '" . implode(",", $logfiles_array) . "', " . $total . ");";
$dbh->exec($query);
*/
$query = "
	INSERT OR IGNORE INTO agent_module
		(moduleName, version, installDate)
	VALUES
		('eventLog', '" . $eversion . "', '" . date('Y-m-d H:i:s', $runTimeEvent) . "');
	UPDATE agent_module
	SET
		lastRan = '" . date('Y-m-d H:i:s', $runTimeEvent) . "',
		version = '" . $eversion . "'
	WHERE
		moduleName = 'eventLog';";
$avarice_dbh->exec($query);

?>
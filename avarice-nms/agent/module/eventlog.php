<?php
$eversion = "0.0.1";

function win_time($timestr)
{
	return substr($timestr, 0, 4) . "-" . substr($timestr, 4, 2) . "-" . substr($timestr, 6, 2) . " " . substr($timestr, 8, 2) . ":" . substr($timestr, 10, 2) . ":" . substr($timestr, 12, 2);
}
$runTimeEvent = date('U');
$batchsize = 1000;

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
		CREATE TABLE events
		(
			pkID INTEGER PRIMARY KEY,
			ComputerName TEXT,
			RecordNumber NUMERIC,
			TimeWritten TEXT,
			eventLogID NUMERIC,
			sourceID NUMERIC,
			EventIdentifier NUMERIC,
			EventCode NUMERIC,
			Type NUMERIC,
			Category NUMERIC,
			UserID NUMERIC,
			InsertionStringID NUMERIC
		);
		CREATE TABLE eventLog
		(
			eventLogID INTEGER PRIMARY KEY,
			eventLog TEXT UNIQUE,
			createdDate TEXT,
			modifiedDate TEXT,
			lastEventID NUMERIC
		);
		CREATE TABLE eventLogSourceFile
		(
			eventLogID INTEGER,
			sourceID INTEGER,
			messageFileID INTEGER
		);
		CREATE TABLE InsertionString
		(
			InsertionStringID INTEGER PRIMARY KEY,
			InsertionString TEXT UNIQUE
		);
		CREATE TABLE message
		(
			messageID INTEGER PRIMARY KEY,
			messageFileID INTEGER,
			identifier INTEGER,
			messageTemplate TEXT,
			jenkins1 TEXT,
			jenkins2 TEXT
		);
		CREATE TABLE messageFile
		(
			messageFileID INTEGER PRIMARY KEY,
			file TEXT,
			fileVersion TEXT,
			productVersion TEXT,
			fileCreated TEXT,
			fileModified TEXT,
			createdDate TEXT,
			fileHash TEXT
		);
		CREATE TABLE runLog
		(
			startTime TEXT,
			endTime TEXT,
			status TEXT,
			eventLogs TEXT,
			eventCount INT
		);
		CREATE TABLE source
		(
			sourceID INTEGER PRIMARY KEY,
			source TEXT,
			sourceName TEXT,
			createdDate TEXT
		);
		CREATE TABLE upload
		(
			startTime TEXT,
			endTime TEXT,
			lastEventID INTEGER
		);
		CREATE TABLE User
		(
			UserID INTEGER PRIMARY KEY,
			User TEXT UNIQUE
		);";
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
				else if ($key == 'providerGUID' and !empty($data['value']))
				{
					$providerArray = Win32RegistryIterator($o_Win32Registry = new COM('winmgmts://./root/default:StdRegProv'), HKEY_LOCAL_MACHINE, 'SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\WINEVT\\Publishers\\' . $data['value']);
					foreach ($providerArray as $pkey => $pvalue)
					{
						if ($pkey == '(Default)')
						{
							$messageFiles[$log][$source]['sourceName'] = $data['value'];
						}
						if ($pkey == 'MessageFileName')
						{
							$messageFiles[$log][$source][] = $data['value'];
						}
					}
				}
			}
		}
	}
}

$logfiles_array = array();
$objShell = new COM("Shell.Application");
foreach($messageFiles as $el => $sources)
{
	$query = "
		select
			eventLogID
		from
			eventLog
		where
			eventLog = '" . $el . "';";
	$result = $dbh->query($query)->fetch();
	$eventLogID = $result['eventLogID'];
	if (empty($eventLogID))
	{
		$insertQuery = "
			insert into eventLog
				(eventLog, createdDate)
			values
				('" . $el . "', '" . date('Y-m-d H:i:s', $runTimeEvent) . "');";
		$dbh->exec($insertQuery);
		$result = $dbh->query($query)->fetch();
		$eventLogID = $result['eventLogID'];
	}
	$logfiles_array[$eventLogID] = $el;
	foreach($sources as $source => $files)
	{
		$query = "
			select
				sourceID
			from
				source
			where
				source = '" . $source . "';";
		$result = $dbh->query($query)->fetch();
		$sourceID = $result['sourceID'];
		if (empty($sourceID))
		{
			if (empty($files['sourceName']))
			{
				$insertQuery = "
					insert into source
						(source, createdDate)
					values
						('" . $source . "', '" . date('Y-m-d H:i:s', $runTimeEvent) . "');";
			}
			else
			{
				$insertQuery = "
					insert into source
						(source, sourceName, createdDate)
					values
						('" . $source . "', '" . $files['sourceName'] . "', '" . date('Y-m-d H:i:s', $runTimeEvent) . "');";
			}
			$dbh->exec($insertQuery);
			$result = $dbh->query($query)->fetch();
			$sourceID = $result['sourceID'];
		}
		foreach ($files as $fkey => $filel)
		{
			if ($fkey != 'sourceName')
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
							messageFile
						where
							file = '" . $mf_filename . "'
							and fileVersion = '" . $fileVersion . "'
							and productVersion = '" . $productVersion . "'
							and fileModified = '" . $fileModified . "'
							and fileHash = '" . $file_hash . "';";
					$result = $dbh->query($query)->fetch();
					$messageFileID = $result['messageFileID'];
					// if file !exists use wrcinfo.exe to get messages
					if (empty($messageFileID))
					{
						$insertQuery = "
							insert into messageFile
								(file, fileVersion, productVersion, fileCreated, fileModified, createdDate, fileHash)
							values
								('" . $mf_filename . "','" . $fileVersion . "','" . $productVersion . "','" . $fileCreated . "','" . $fileModified . "','" . date('Y-m-d H:i:s', $runTimeEvent) . "','" . $file_hash . "');";
						$dbh->exec($insertQuery);
						$result = $dbh->query($query)->fetch();
						$messageFileID = $result['messageFileID'];
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
" . $output[	$templateLineID + $x];
								$x++;
							}
							$messageTemplate = str_replace("'", "''", $messageTemplate);
							$jenks = substr($output[$templateLineID + $x], 15);
							list($jenkins1, $jenkins2) = explode(",", $jenks);
							$insertQuery = "
								insert into message
									(messageFileID, identifier, messageTemplate, jenkins1, jenkins2)
								values
									('" . $result['messageFileID'] . "', '" . $identifier . "', '" . $messageTemplate . "', '" . $jenkins1 . "', '" . $jenkins2 . "');";
							$dbh->exec($insertQuery);
						}
					}
					$query = "
						select
							count(*) as 'Count'
						from eventLogSourceFile
						where
							eventLogID = " . $eventLogID . "
							and sourceID = " . $sourceID . "
							and messageFileID = " . $messageFileID . ";";
					$result = $dbh->query($query)->fetch();
					if ($result['Count'] < 1)
					{
						$insertQuery = "
							insert into eventLogSourceFile
								(eventLogID, sourceID, messageFileID)
							values
								(" . $eventLogID . ", " . $sourceID . ", " . $messageFileID . ");";
						$dbh->exec($insertQuery);
						$result = $dbh->query($query)->fetch();
					}
				}
			}
		}
	}
}

// Make WMI connection
$objWMIService = new COM("winmgmts:{impersonationLevel=impersonate,authenticationLevel=pktPrivacy,(Security)}!//.\\root\\cimv2");
$total = 0;

$emptyvariant = $objWMIService->ExecQuery("Select * from Win32_NTLogEvent WHERE RecordNumber = 'string'",'WQL',48);

foreach ($logfiles_array as $logfileID => $logfilename)
{
	$x = 0;
	$query = "
		SELECT
			*
		FROM eventLog
		WHERE
			eventLogID = '" . $logfileID . "';";
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
	$colItems = $objWMIService->ExecQuery("Select * from Win32_NTLogEvent WHERE LogFile = '" . $logfilename . "' AND RecordNumber > " . $result['lastEventID'],'WQL',48);
	if ($colItems != $emptyvariant)
	{
		$query = "BEGIN TRANSACTION; ";
		$norm_query = "BEGIN TRANSACTION; ";
		foreach ($colItems as $objItem)
		{
			$norm_query .= "
				INSERT OR IGNORE INTO User (User) VALUES ('" . $objItem->User . "');";
			$norm_query .= "
				INSERT OR IGNORE INTO InsertionString (InsertionString) VALUES ('";
			$insertionStrings = array();
			if ($objItem->InsertionStrings != NULL)
			{
				foreach ($objItem->InsertionStrings as $oiv)
				{
					$insertionStrings[] = $oiv;
				}
			}
			$norm_query .= trim(str_replace("'", "''", implode(",", $insertionStrings))) . "'); ";
			if ($x >= $batchsize)
			{
				$dbh->exec($norm_query .= " COMMIT;");
				$norm_query = "BEGIN TRANSACTION;";
			}
			$query .= "
				INSERT INTO Events (ComputerName, RecordNumber, TimeWritten, eventLogID, sourceID, EventIdentifier, EventCode, Type, Category, UserID, InsertionStringID) VALUES
					(
						'" . $objItem->ComputerName . "',
						'" . $objItem->RecordNumber . "',
						'" . win_time($objItem->TimeWritten) . "',
						(
							SELECT
								eventlogID
							FROM eventLog
							WHERE
								eventLog = '" . $objItem->LogFile . "'
						),
						(
							SELECT
								sourceID
							FROM source
							WHERE
								source = '" . $objItem->SourceName . "'
						),
						'" . $objItem->EventIdentifier . "',
						'" . $objItem->EventCode . "',
						'" . $objItem->EventType . "',
						'" . $objItem->Category . "',
						(
							SELECT
								UserID
							FROM User
							WHERE
								User = '" . $objItem->User . "'
						),
						(
							SELECT
								InsertionStringID
							FROM InsertionString
							WHERE
								InsertionString = '" . trim(str_replace("'", "''", implode(",", $insertionStrings))) . "'
						)
					);";
			if ($x < $batchsize)
			{
				$x++;
			}
			else
			{
				$total += $x;
				$x = 0;
				$dbh->exec($norm_query . " COMMIT;");
				$dbh->exec($query . " COMMIT;");
				$query = "BEGIN TRANSACTION;";
				$norm_query = "BEGIN TRANSACTION;";
			};
		};
		$dbh->exec($norm_query . " COMMIT;");
		$dbh->exec($query . " COMMIT;");
		$total += $x;
	};
	$query = "
		SELECT
			IFNULL(MAX(RecordNumber), 0) AS 'RecordNumber'
		FROM Events
		WHERE
			eventLogID = " . $logfileID . ";";
	$result = $dbh->query($query)->fetch();
	if ($result['RecordNumber'] == 4294967295)
	{
		$result['RecordNumber'] = 0;
	};
	$query = "
		INSERT OR IGNORE INTO eventLog
			(createdDate, eventLog)
		VALUES
			('" . date('Y-m-d H:i:s', $runTimeEpoch) . "', '" . $logfilename . "');
		UPDATE eventLog
		SET
			modifiedDate = '" . date('Y-m-d H:i:s', $runTimeEvent) . "',
			lastEventID = " . $result['RecordNumber'] . "
		WHERE
			eventLog = '" . $logfilename . "';
	";
	$dbh->exec($query);
};

$query = "
	INSERT INTO runLog
		(startTime, endTime, status, eventLogs, eventCount)
	VALUES
		('" . date('Y-m-d H:i:s', $runTimeEvent) . "', '" . date('Y-m-d H:i:s') . "', 'success', '" . implode(",", $logfiles_array) . "', " . $total . ");";
$dbh->exec($query);

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
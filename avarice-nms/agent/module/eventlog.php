<?php

// Determines if this is the first run and creates tables if it is
$query = "
	SELECT
		COUNT(*) as 'Count'
	FROM agent_module
	WHERE
		moduleName = 'eventLog';";
$result = $avarice_dbh->query($query)->fetch();
$firstRun = $result['Count'];

if ($firstRun == 0)
{
	$query = "
		CREATE TABLE eventLog_logFiles
		(
			createdDate TEXT,
			modifiedDate TEXT,
			logFile TEXT UNIQUE,
			lastEventID TEXT
		)
		CREATE TABLE eventLog_runLog
		(
			startTime TEXT,
			endTime TEXT,
			status TEXT,
			eventLogs TEXT,
			eventCount INT
		);
}

$snorm = array(
	"Template"        => "Templates",
	"InsertionString" => "InsertionStrings",
	"Category"        => "Categories",
	"EventCode"       => "EventCodes",
	"LogFile"         => "Logfiles",
	"SourceName"      => "SourceNames",
	"Type"            => "Types",
	"User"            => "Users"
);

// Create sqlite DB for current dump
try {$dbh = new PDO("sqlite:" . $config['eventLog']['path'] . "/eventLog." . date('Ymd.Hi', $runTimeEpoch) . ".sqlite3");
	$query = "CREATE TABLE Events (pkID INTEGER PRIMARY KEY, ComputerName TEXT, Message TEXT, RecordNumber NUMERIC, TimeWritten TEXT, ";
	$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	$dbh->exec("PRAGMA journal_mode = MEMORY; PRAGMA temp_store = MEMORY; PRAGMA synchronous = OFF");
	foreach ($snorm as $key => $value) {
		$dbh->exec("CREATE TABLE " . $value . " (pkID INTEGER PRIMARY KEY, " . $key . " TEXT UNIQUE)");
		$query .= $key . "ID INT, ";
	};
	$query = substr($query, 0, -2) . ")";
	$dbh->exec($query);
} catch(PDOException $e) {
	echo $e->getMessage();
};

// Make WMI connection
$objWMIService = new COM("winmgmts:{impersonationLevel=impersonate,authenticationLevel=pktPrivacy,(Security)}!//.\\root\\cimv2");

// Gather list of EventLog Files
$logFileDetails = $objWMIService->ExecQuery("Select * from Win32_NTEventLogFile",'WQL',48);
$logfiles_array = array();
foreach ($logFileDetails as $logFileDetail) {
	$logfiles_array[] = $logFileDetail->LogFileName;
};

foreach ($logfiles_array as $logfilename){
	$colItems = $objWMIService->ExecQuery("Select * from Win32_NTLogEvent WHERE LogFile = '" . $logfilename . "' AND TimeWritten >= ",'WQL',48);

?>
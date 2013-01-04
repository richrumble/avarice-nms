<?php
//header("Content-Type: text/plain");

// Always use UTC
date_default_timezone_set('UTC');

// Get the run time and calculalte the minute of the day
$runTimeEpoch = date('U');

// Load config
$config = json_decode(file_get_contents('./config.json'), TRUE);

// Determine if data file exists
$avarice_dbExists = TRUE;
if (!is_file($config['avariceDataFile']['path']))
{
	$avarice_dbExists = FALSE;
}

// connects to local database / creates it if need be
try {
	$avarice_dbh = new PDO("sqlite:" . $config['avariceDataFile']['path']);
}
catch(PDOException $e)
{
	echo $e->getMessage();
}

// Creates default tables if this is the fist run
if ($avarice_dbExists == FALSE)
{
	$query = "
		CREATE TABLE agent_runLog
		(
			startTime TEXT,
			endTime TEXT,
			status TEXT,
			modulesRan TEXT
		);
		CREATE TABLE agent_errorLog
		(
			pkID INTEGER PRIMARY KEY,
			errorTime TEXT,
			error TEXT
		);
		CREATE TABLE agent_module
		(
			moduleName TEXT UNIQUE,
			version TEXT,
			installDate TEXT,
			lastRan TEXT
		);";
	$avarice_dbh->exec($query);
}

// List of modules that have ben installed (ran at least once)
$modulesInstalled = array();
$query = "
	SELECT
		moduleName,
		lastRan
	FROM agent_module;";
foreach ($avarice_dbh->query($query) as $row)
{
	$modulesInstalled[$row['moduleName']] = $row['lastRan'];
}
		
// Determines which modules to run based on the interval defined in the config
$runModules = array();
foreach ($config['module'] as $key => $value)
{
	if (!isset($value['disabled']) or $value['disabled'] == 0)
	{
		if (in_array($key, array_keys($modulesInstalled)))
		{
			$minutes =  floor(($runTimeEpoch - date('U', strtotime($modulesInstalled[$key]))) / 60);
			if ($minutes >= $value)
			{
				$runModules[] = $key;
			}
		}
		else
		{
			$runModules[] = $key;
		}
	}
}

// Include only those modules that are going to be run
foreach($runModules as $runModule)
{
	include_once("./module/" . $runModule . ".php");
}

// Need to create runLog, errorLog and loaded module default tables.
$endEpoch = date('U');
$query = "
	INSERT INTO agent_runLog
		(startTime, endTime, modulesRan)
	VALUES
		('" . date('Y-m-d H:i:s', $runTimeEpoch) . "', '" . date('Y-m-d H:i:s', $endEpoch) . "', '" . implode(",", $runModules) . "');";
$avarice_dbh->exec($query);

?>
<?php
//header("Content-Type: text/plain");

// Always use UTC
date_default_timezone_set('UTC');

// Get the run time and calculalte the minute of the day
$runTimeEpoch = date('U');
$minuteOfDay  = (date('H', $runTimeEpoch) * 60) + (date('i', $runTimeEpoch));

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
	// Need to create runLog, errorLog and loaded module default tables.
}
catch(PDOException $e)
{
	echo $e->getMessage();
}

// Determines which modules to run based on the interval defined in the config
$runModules = array();
foreach ($config as $key => $value)
{
	if (
			(isset($value['intervalMinutes']))
			and
			($minuteOfDay % $value['intervalMinutes'] == 0)
		)
	{
		$runModules[] = $key;
	}
}

// Include only those modules that are going to be run
foreach($runModules as $runModule)
{
	include_once("./module/" . $runModule . ".php");
}

?>
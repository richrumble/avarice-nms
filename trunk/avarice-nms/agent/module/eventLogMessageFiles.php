<?php

exec("reg.exe query HKLM\SYSTEM\CurrentControlSet\services\eventlog /s", $output);

$messageFiles = array();
$x = 0;
foreach ($output as $line)
{
	$line = trim($line);
	if (!empty($line))
	{
		$compString = substr($line, 0, 61);
		if ($compString == "HKEY_LOCAL_MACHINE\\SYSTEM\\CurrentControlSet\\services\\eventlog")
		{
			$hkey = explode("\\", $line);
			if (count($hkey) == 7)
			{
				$eventSource = $hkey[6];
				if (empty($messageFiles[$eventLog][$eventSource]))
				{
					$messageFiles[$eventLog][$eventSource] = array();
				}
			}
			else if (count($hkey) == 6)
			{
				$eventSource = "";
				$eventLog    = $hkey[5];
				if (empty($messageFiles[$eventLog]))
				{
					$messageFiles[$eventLog] = array();
				}
			}
			else
			{
				$eventSource = "";
				$eventLog    = "";
			}
		}
		else
		{
			if (!empty($eventSource))
			{
				$hvalue = explode("    ", $line);
				if (substr($hvalue[0], -11) == "MessageFile" and !empty($hvalue[2]))
				{
					$messageFiles[$eventLog][$eventSource][] = $hvalue[2];
				}
			}
		}
	}
}

print_r($messageFiles);

?>
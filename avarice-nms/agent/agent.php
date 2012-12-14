<?php
header("Content-Type: text/plain");

date_default_timezone_set('UTC');

$config       = json_decode(file_get_contents('./config.json'), TRUE);
$runTimeEpoch = date('U');
print date('Y-m-d H:i:s', $runTimeEpoch) . "\n\n";

print_r($config);
?>
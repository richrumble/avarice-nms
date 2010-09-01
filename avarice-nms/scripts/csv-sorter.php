/*
 +--------------------------------------------------------------------------+
 | Copyright (C) 2009-2010 Xinn.org                                         |
 |                                                                          |
 | This program is free software; you can redistribute it and/or            |
 | modify it under the terms of the GNU General Public License              |
 | as published by the Free Software Foundation; either version 2           |
 | of the License, or (at your option) any later version.                   |
 |                                                                          |
 | This program is distributed in the hope that it will be useful,          |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
 | GNU General Public License for more details.                             |
 +--------------------------------------------------------------------------+
 |Avarice-nms:A greedy and insatiable inventory and network managment system|
 +--------------------------------------------------------------------------+
 | This code is designed, written, and maintained by the Xinn.org. See      |
 | about.php and/or the AUTHORS file for specific developer information.    |
 +--------------------------------------------------------------------------+
 | http://avarice-nms.com                                                   |
 | http://avarice-nms.info                                                  |
 | http://xinn.org/avarice.php                                              |
 +--------------------------------------------------------------------------+
*/
<?php

if (empty($argv[1]) or !is_file($argv[1])) {
  exit("You must provide a file to sort: php csv-sorter.php \\path\\to\\file.csv\n");
} else {
  $filename = $argv[1];
  $output_filename = substr($argv[1], 0, -4) . "-sorted.csv";
};

if (($handle = fopen($filename, "r")) !== FALSE) {
  $header_array = array(); $row = 1;
  while (($data = fgetcsv($handle)) !== FALSE) {
    $temp_string = "";
    if ($row == 1) {
      $header_array = $data;
      asort($header_array);
      if (($handle_out = fopen($output_filename, "w")) === FALSE) {
        exit("Could not open or create " . $output_filename . ".\n");
      };
    };
    foreach ($header_array as $key => $discard) {
      $temp_string .= "\"" . $data[$key] . "\",";
    };
    fwrite($handle_out, substr($temp_string, 0, -1) . "\n");
    $row++;
  };
  fclose($handle_out);
} else {
  exit("Could not open " . $filename . ".\n");
};
fclose($handle);

?>
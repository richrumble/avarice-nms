<?php

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

include_once("../../include/config.php");

$process_time = gmdate('Y-m-d-H-i');

$xml_path = mysql_result(dbquery_func($avarice_user_connection, "SELECT value FROM inv__config WHERE parameter = \"xml_path\""), 0, 0);
$handle = opendir($xml_path)) {
  $file_list = array();
  while (false !== ($file = readdir($handle))) {
    if (!in_array($file, array(".", "..")) and is_file($xml_path . "/" . $file) and substr($file, -4) == ".xml") {{
      $file_list[] = $file;
    };
  };
  closedir($handle);
};

foreach ($file_list as $file) {
  
};

?>
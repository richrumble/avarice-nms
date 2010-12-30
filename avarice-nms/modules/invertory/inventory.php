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

function get_hash_ID($string) {
  global $avarice_user_connection;
  global $avarice_admin_connection;
  $hash = hash('md5', $string);
  $query = "
            SELECT hash_ID
              FROM inv__hash
             WHERE hash = \"" . $hash . "\"";
  $result = dbquery_func($avarice_user_connection, $query);
  if (mysql_num_rows($result) == 1) {
    return mysql_result($result, 0, 0);
  } else {
    $query = "
              INSERT INTO inv__hash (hash)
                   VALUES (\"" . $hash . "\")";
    dbquery_func($avarice_admin_connection, $query);
    $query = "
              SELECT hash_ID
                FROM inv__hash
               WHERE hash = \"" . $hash . "\"";
    return mysql_result(dbquery_func($avarice_user_connection, $query), 0, 0);
  };
};

$process_time = gmdate('Y-m-d-H-i');

$xml_path = mysql_result(dbquery_func($avarice_user_connection, "SELECT value FROM inv__config WHERE parameter = \"xml_path\""), 0, 0);
if ($handle = opendir($xml_path)) {
  $file_list = array();
  while (false !== ($file = readdir($handle))) {
    if (!in_array($file, array(".", "..")) and is_file($xml_path . "/" . $file) and substr($file, -4) == ".xml") {
      $file_list[] = $file;
    };
  };
  closedir($handle);
};

foreach ($file_list as $file) {
  print "Starting file " . $file . "\n";
  $xml = simplexml_load_file($xml_path . "/" . $file);
  foreach($xml->Asset as $asset) {
    $query = "
              INSERT INTO inv__asset (Name)
                   VALUES (\"" . $asset->Name . "\")";
    dbquery_func($avarice_admin_connection, $query);
    $asset_ID = mysql_result(dbquery_func($avarice_user_connection, "SELECT Asset_ID FROM inv__asset WHERE Name = \"" . $asset->Name . "\""), 0, 0);
    foreach($asset->Drives as $drive) {
      $hash_ID = get_hash_ID($drive->Description . $drive->DeviceID . $drive->Name . $drive->ProviderName);
      $query = "
                INSERT INTO inv__drives (Asset_ID, hash_ID, Description, DeviceID, Name, ProviderName)
                     VALUES (\"" . $asset_ID . "\", \"" . $hash_ID . "\", \"" . $drive->Description . "\", \"" . $drive->DeviceID . "\", \"" . $drive->Name . "\", \"" . $drive->ProviderName . "\")";
      dbquery_func($avarice_admin_connection, $query);
    };
  };
  
  
  
  
//  $gzhandle = gzopen($xml_path . "/" . $process_time . ".processed.gz", 'a9');
//  gzwrite($gzhandle, file_get_contents($xml_path . "/" . $file));
//  gzclose($gzhandle);
//  unlink($xml_path . "/" . $file);
  print "Finished file " . $file . "\n";
};

?>
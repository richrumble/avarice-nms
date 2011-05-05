<?php

include_once("../../include/config.php");
include_once("inv_functions.php");
include_once("inv_config.php");

$result = dbquery_func($avarice_user_connection, "SELECT value FROM inv__config_server WHERE parameter = 'xml_path'");
$xml_path_string = mysql_result($result, 0, 0);
$xml_path = dir($xml_path_string);
while (FALSE !== ($file = $xml_path->read())) {
  if (if_file($xml_path . "/" . $file) and (is_numeric(substr($file, 0, 8)) and substr($file, -4) == ".xml")) {
    $query = "
              INSERT INTO inv__dataprocessing
                          (createdDate, assetName, data)
                   VALUES ('" . substr($file, 0, 4) . "-" . substr($file, 4, 2) . "-" . substr($file, 6, 2) . "', '" . substr($file, 8, -4) . "', '" . file_get_contents($file) . "');";
    dbquery_func($avarice_admin_connection, $query);
    if (mysql_affected_rows() > 0) {
      unlink($xml_path_string . "/" . $file);
    };
  };
};

?>
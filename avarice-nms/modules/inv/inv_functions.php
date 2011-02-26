<?php

function getHashID($hash) {
  global $avarice_admin_connection;
  $query = "SELECT hash_ID FROM inv__hash WHERE hash = '" . $hash . "'";
  $result = dbquery_func($avarice_admin_connection, $query);;
  if (mysql_num_rows($result) != 1) {
    dbquery_func($avarice_admin_connection, "INSERT INTO inv__hash SET hash = '" . $hash . "'");
    $result = dbquery_func($avarice_admin_connection, $query);
  };
  return mysql_result($result, 0, 0);
};

?>
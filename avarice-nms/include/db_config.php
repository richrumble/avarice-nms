<?php

$avarice_user_connection   = array("db_type"  => "mysql",
                                   "db_host"  => "localhost",
                                   "db_port"  => "3306",
                                   "db_name"  => "avarice_nms",
                                   "username" => "avarice_user",
                                   "password" => "We8AcQTvDXwqJsmz");

$avarice_admin_connection = array("db_type"  => "mysql",
                                  "db_host"  => "localhost",
                                  "db_port"  => "3306",
                                  "db_name"  => "avarice_nms",
                                  "username" => "avarice_admin",
                                  "password" => "KcQMFBQZFpmP9tmu");

function dbquery_func($connection_info, $query, $debug="off") {
  if ($connection_info['db_type'] == "mysql") {
    $port_deliminator = ":";
  } else if ($connection_info['db_type'] == "mssql") {
    $port_deliminator = ",";
  };
  call_user_func_array($connection_info['db_type'] . "_connect", array($connection_info['db_host'] . $port_deliminator . $connection_info['db_port'], $connection_info['username'], $connection_info['password'])) or die("Unable to connect to " . $connection_info['db_host']);
  call_user_func_array($connection_info['db_type'] . "_select_db", array($connection_info['db_name'])) or die("Unable to select database " . $connection_info['db_name']);
  $return     = call_user_func($connection_info['db_type'] . "_query", $query);
  if ($debug == "on") {
    if ($connection_info['db_type'] == "mysql") {
      $merror   = mysql_error();
//      $rows_returned = mysql_num_rows($return);
    } else if ($connection_info['db_type'] == "mssql") {
      $merror = mssql_get_last_message();
//      $rows_returned = mssql_num_rows($return);
    };
    if (!empty($merror)) {
      print strtoupper($connection_info['db_type']) . " Error:<br />" . $merror . "<p />Query<br />: " . $query . "<br />";
    };
//    print "Number of rows returned: " . $rows_returned . "<br />";
  };
  return $return;
};

?>
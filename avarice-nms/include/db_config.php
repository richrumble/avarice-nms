<?php

$avarice_user_connection   = array("db_type"  => "mysql",
                                   //"db_host"  => "10.10.251.58",
                                   "db_host"  => "localhost",
                                   "db_port"  => "3306",
                                   "db_name"  => "avarice_nms",
                                   "username" => "avarice_user",
                                   "password" => "We8AcQTvDXwqJsmz");

$avarice_admin_connection = array("db_type"  => "mysql",
                                  //"db_host"  => "10.10.251.58",
                                  "db_host"  => "localhost",
                                  "db_port"  => "3306",
                                  "db_name"  => "avarice_nms",
                                  "username" => "avarice_admin",
                                  "password" => "KcQMFBQZFpmP9tmu");

function dbquery_func($connection_info, $query, $debug="off") {
  if ($connection_info['db_type'] == "mysql") {
    mysql_connect($connection_info['db_host'] . ":" . $connection_info['db_port'], $connection_info['username'], $connection_info['password']) or die("Unable to connect to " . $connection_info['db_host']);
    mysql_select_db($connection_info['db_name']) or die("Unable to select database " . $connection_info['db_name']);
    $return     = mysql_query($query);
    if ($debug == "on") {
      $merror   = mysql_error();
      if (!empty($merror)) {
        print "MySQL Error:<br />" . $merror . "<p />Query<br />: " . $query . "<br />";
      };
      print "Number of rows returned: " . mysql_num_rows($return) . "<br />";
    };
  } else if ($connection_info['db_type'] == "mssql") {
    mssql_connect($connection_info['db_host'] . "," . $connection_info['db_port'], $connection_info['username'], $connection_info['password']) or die("Unable to connect to " . $connection_info['db_host'] . "<br />" . $query);
    mssql_select_db($connection_info['db_name']) or die("Unable to select database " . $connection_info['db_name']);
    $return     = mssql_query($query);
    if ($debug == "on") {
      $merror = mssql_get_last_message();
      if (!empty($merror)) {
        print "MySQL Error: " . $merror . "<br />Query" . $query . "<br />";
      };
      print "Number of rows returned: " . mssql_num_rows($result) . "<br />";
    };
  };
  return $return;
};

?>
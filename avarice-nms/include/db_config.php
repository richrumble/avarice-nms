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

function dbquery_func_new($connection_info, $query, $debug) {
  if ($connection_info['db_type'] == "mysql") {
    if (!is_array($query)) {
      return FALSE;
    };
    $link = new mysqli($connection_info['db_host'], $connection_info['username'], $connection_info['password'], $connection_info['db_name'], $connection_info['db_port']);
    if ($link->connect_error) {
      die("Connection Error (" . $mysqli->connect_errno . ") - " . $mysqli->connect_error);
    };
    if ($stmt = $link->prepare($query['query'])) {
      call_user_func_array(array($stmt, 'bind_param'), refvalues($query['params']));
      $stmt->execute();
      $meta = $stmt->result_metadata();
      $parameters = array(); $results = array();
      while ($field = $meta->fetch_field()) {
        $parameters[] = &$row[$field->name];
      };
      call_user_func_array(array($stmt, 'bind_result'), refvalues($parameters));
      while ($stmt->fetch()) {
       $x = array();
       foreach ($row as $key => $val) {
          $x[$key] = $val;
       };
       $results[] = $x;
      };
      $stmt->close();
      $mysqli->close();
      return $result;
    };
  };

};
function refvalues($values){
  if (strnatcmp(phpversion(),'5.3') >= 0) {
    $refs = array();
    foreach($values as $key => $value)
      $refs[$key] = &$values[$key];
    return $refs;
  };
  return $values;
};

?>
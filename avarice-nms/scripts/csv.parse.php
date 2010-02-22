<?php

include_once("../include/config.php");

$start_time = microtime_float();

if (empty($argv[1]) or !is_file($argv[1])) {
  exit("You must provide a file to parse: php csv.parse.php \\path\\to\\file.csv\n");
} else {
  $file = $argv[1];
};

function ldap_to_db_structure($table_array, $avarice_admin_connection) {
  foreach ($table_array as $objectClass => $details) {
    if (empty($objectClass)) continue;
    $table_exists_result = dbquery_func($avarice_admin_connection, "SHOW TABLES LIKE '" . str_replace("-", "_", $objectClass) . "'");
    if (mysql_num_rows($table_exists_result) == 0) {
      $create_table_query = "CREATE TABLE avarice_nms." . str_replace("-", "_", $objectClass) . " (";
      foreach ($details['field_details'] as $key => $varray) {
        if (isset(${$objectClass . "_first_column_done"})) {
          $create_table_query .= ", ";
        } else {
          ${$objectClass . "_first_column_done"} = 1;
        };
        $create_table_query .= "" . str_replace("-", "_", $key) . " ";
        if ($varray['is_numeric'] != 1) {
          $create_table_query .= "VARCHAR";
        } else {
          $create_table_query .= "INT";
        };
        $create_table_query .= "( " . $varray['length'] . " ) NULL ";
      };
      $create_table_query .= ") ENGINE = INNODB";
      dbquery_func($avarice_admin_connection, $create_table_query);
    } else {
      $field_list_result =  dbquery_func($avarice_admin_connection, "SHOW COLUMNS FROM avarice_nms." . str_replace("-", "_", $objectClass), "on");
      $fields_exist_details = array();
      while ($field_row = mysql_fetch_assoc($field_list_result)) {
        $fields_exist_details[$field_row['Field']] = array("Type"    => $field_row['Type'],
                                                           "Null"    => $field_row['Null'],
                                                           "Key"     => $field_row['Key'],
                                                           "Default" => $field_row['Default'],
                                                           "Extra"   => $field_row['Extra']);
      };
      $additional_fields = array_diff_key($details['field_details'], $fields_exist_details);
      if (count($additional_fields) > 0) {
        $add_query = "ALTER TABLE " . $objectClass . " ";
        foreach ($additional_fields as $new_field => $junk) {
          if (!isset($first_add)) {
            $first_add = "true";
          } else {
            $add_query .= ", ";
          };
          $add_query .= "ADD `" . str_replace("-", "_", $new_field) . "` ";
          if ($details['field_details'][$new_field]['is_numeric'] != 1) {
            $add_query .= "VARCHAR";
          } else {
            $add_query .= "INT";
          };
          $add_query .= "( " . $details['field_details'][$new_field]['length'] . " ) NULL";
        };
        dbquery_func($avarice_admin_connection, $add_query);
      };
    };
  };
};

function ldap_to_db_data($table_array, $avarice_admin_connection) {
  foreach ($table_array as $objectClass => $details) {
    if (empty($objectClass)) continue;
    $insert_query = "INSERT INTO avarice_nms." . str_replace("-", "_", $objectClass) . " (";
    foreach ($details['field_details'] as $column => $junk) {
      if (!isset($first_insert_column)) {
        $first_insert_column = "true";
      } else {
        $insert_query .= ", ";
      };
      $insert_query .= str_replace("-", "_", $column);
    };
    unset($first_insert_column);
    $insert_query .= ") VALUES ";
    foreach ($details['data'] as $key => $data) {
      if (!isset($first_line_data_done)) {
        $first_line_data_done = "true";
      } else {
        $insert_query .= ", ";
      };
      $insert_query .= "(";
      foreach ($data as $key => $value) {
        if (isset($first_data_done)) {
          $insert_query .= ", ";
        } else {
          $first_data_done = 1;
        };
        $insert_query .= "\"" . addslashes($value) . "\"";
      };
      $insert_query .= ")";
      unset($first_data_done);
    };
    unset($first_line_data_done);
    dbquery_func($avarice_admin_connection, $insert_query, "on");
  };
};

if (($handle = fopen($file, "r")) !== FALSE) {
  $objectclass_array = array();
  $header_array      = array();
  $current_row       = 1;
  $batch_counter     = 1;
  while (($data = fgetcsv($handle)) !== FALSE) {
    $number_of_fields = count($data);
    if ($current_row == 1) {
      for ($c=0; $c < $number_of_fields; $c++) {
        $header_array[$c] = $data[$c];
      };
      $oc_index = array_search("objectClass", $header_array);
    } else {
      if (!isset($objectclass_array[$data[$oc_index]])) {
        $objectclass_array[$data[$oc_index]] = array("field_details" => array(),
                                                     "data"          => array());
        ${$data[$oc_index] . "_counter"} = 1;
      } else {
        ${$data[$oc_index] . "_counter"}++;
      };
      for ($c=0; $c < $number_of_fields; $c++) {
        if ($c != $oc_index and !empty($data[$c])) {
          $objectclass_array[$data[$oc_index]]['data'][${$data[$oc_index] . "_counter"}][$header_array[$c]] = $data[$c];
          if (!in_array($header_array[$c], $objectclass_array[$data[$oc_index]]['field_details'])) {
            $objectclass_array[$data[$oc_index]]['field_details'][$header_array[$c]] = array("is_numeric" => is_numeric($data[$c]),
                                                                                             "length"     => strlen($data[$c]));
          } else {
            if (is_numeric($data[$c]) !== $objectclass_array[$data[$oc_index]]['field_details'][$header_array[$c]]['is_numeric']) {
              $objectclass_array[$data[$oc_index]]['field_details'][$header_array[$c]]['is_numeric'] = "string";
            };
            if (strlen($data[$c]) > $objectclass_array[$data[$oc_index]]['field_details'][$header_array[$c]]['length']) {
              $objectclass_array[$data[$oc_index]]['field_details'][$header_array[$c]]['length'] = strlen($data[$c]);
            };
          };
        };
      };
    };
    if ($batch_counter == 500) {
      ldap_to_db_structure($objectclass_array, $avarice_admin_connection);
      ldap_to_db_data($objectclass_array, $avarice_admin_connection);
      $objectclass_array = array();
      $batch_counter = 1;
    } else {
      $batch_counter++;
    };
    $current_row++;
  };
  fclose($handle);
};

ldap_to_db_structure($objectclass_array, $avarice_admin_connection);
ldap_to_db_data($objectclass_array, $avarice_admin_connection);

$parse_time = microtime_float();



$end_time = microtime_float();
$parse_time_taken = $parse_time - $start_time;
$db_creation_time = $end_time - $parse_time;
$total_time_taken = $end_time - $start_time;
print "CSV Parsed and DBs created\n";
print "CSV file parsed in " . $parse_time_taken . " seconds\n";
print "DB tables created and data entered in " . $db_creation_time . " seconds\n";
print "Total time taken: " . $total_time_taken . " seconds\n";
print $current_row . "\n";

?>
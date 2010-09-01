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

$start_time = microtime_float();

if (is_cli()) {
  if (empty($argv[1]) or !is_file($argv[1])) {
    exit("You must provide a file to parse: php ldap_csv_DB \\path\\to\\file.csv\n");
  } else {
    $file = $argv[1];
  };
  if (!empty($argv[2]) and is_numeric($argv[2])) {
    $batchSizeSpec = $argv[2];
  } else {
    $batchSizeSpec = 500;
  };
  $line_break = "\n";
} else {
  if (empty($form_data['file']) or !is_file($form_data['file']) or !is_numeric($form_data['batchSizeSpec'])) {
    print "
           <form action=\"" .  $_SERVER['PHP_SELF'] . "\" method=\"post\">
            <label for=\"file\">Path to file:</label> <input type=\"text\" id=\"file\" name=\"file\" /><br />
            <lable for=\"batchSizeSpec\">Batch Size:</label> <input type=\"text\" id=\"batchSizeSpec\" name=\"batchSizeSpec\" value=\"500\" /><br />
            <input type=\"submit\" value=\"Submit\" />
           </form>
    ";
    exit();
  } else {
    $file = $form_data['file'];
    $batchSizeSpec = $form_data['batchSizeSpec'];
    $line_break = "<br />";
  };
};

function charreplace($char) {
  $return = trim(str_replace(array("/", "\\", "-", ";", "=", ":", "*", "?", "\"", "'", "<", ">", "|", ".", "`"), "_", $char));
  return $return;
};

function ldap_to_db_structure($table_array, $avarice_admin_connection) {
  $func_start_time = microtime_float();
  foreach ($table_array as $objectClass => $details) {
    if (empty($objectClass)) continue;
    $table_exists_result = dbquery_func($avarice_admin_connection, "SHOW TABLES LIKE '" . charreplace($objectClass) . "'");
    if (mysql_num_rows($table_exists_result) == 0) {
      $create_table_query = "CREATE TABLE " . $avarice_admin_connection['db_name'] . "." . charreplace($objectClass) . " (" . charreplace($objectClass) . "_ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY";
      foreach ($details['field_details'] as $key => $varray) {
        $create_table_query .= ", " . charreplace($key) . " ";
        if ($varray['is_numeric'] != 1) {
          if ($varray['length'] < 255) {
            $create_table_query .= "VARCHAR( " . $varray['length'] . " )";
          } else {
            $create_table_query .= "LONGTEXT";
          };
        } else {
          $create_table_query .= "INT";
        };
        $create_table_query .= " NULL ";
      };
      if (array_key_exists("DN", $details['field_details'])) {
        if ($details['field_details']["DN"]['length'] < 255) {
          $create_table_query .= ", UNIQUE (DN)";
        };
      };
      $create_table_query .= ") ENGINE = INNODB";
      dbquery_func($avarice_admin_connection, $create_table_query, "on");
    } else {
      $field_list_result =  dbquery_func($avarice_admin_connection, "SHOW COLUMNS FROM " . $avarice_admin_connection['db_name'] . "." . charreplace($objectClass));
      $fields_exist_details = array();
      while ($field_row = mysql_fetch_assoc($field_list_result)) {
        if (isset($first_field_row_done)) {
          $fields_exist_details[$field_row['Field']] = array("Type"    => $field_row['Type'],
                                                             "Null"    => $field_row['Null'],
                                                             "Key"     => $field_row['Key'],
                                                             "Default" => $field_row['Default'],
                                                             "Extra"   => $field_row['Extra']);
        } else {
          $first_field_row_done = "true";
        };
      };
      $additional_fields = array_diff_key($details['field_details'], $fields_exist_details);
      if (count($additional_fields) > 0) {
        $add_query = "ALTER TABLE " . $avarice_admin_connection['db_name'] . "." . charreplace($objectClass) . " ";
        foreach ($additional_fields as $new_field => $junk) {
          if (!isset($first_add)) {
            $first_add = "true";
          } else {
            $add_query .= ", ";
          };
          $add_query .= "ADD `" . charreplace($new_field) . "` ";
          if ($details['field_details'][$new_field]['is_numeric'] != 1) {
            $add_query .= "VARCHAR";
          } else {
            $add_query .= "INT";
          };
          $add_query .= "( " . $details['field_details'][$new_field]['length'] . " ) NULL";
        };
        dbquery_func($avarice_admin_connection, $add_query, "on");
        unset($first_add);
      };
    };
  };
  $func_end_time = microtime_float();
  $func_time_taken = $func_end_time - $func_start_time;
  return $func_time_taken;
};

function ldap_to_db_data($table_array, $avarice_admin_connection) {
  $func_start_time = microtime_float();
  foreach ($table_array as $objectClass => $details) {
    if (empty($objectClass)) continue;
    $column_list_result = dbquery_func($avarice_admin_connection, "SHOW COLUMNS FROM " . $avarice_admin_connection['db_name'] . "." . charreplace($objectClass));
    $column_list = array();
    while ($row = mysql_fetch_assoc($column_list_result)) {
      if (isset($first_field_row_done)) {
        $column_list[] = $row['Field'];
      } else {
        $first_field_row_done = "true";
      };
    };
    $insert_query = "INSERT INTO " . $avarice_admin_connection['db_name'] . "." . charreplace($objectClass) . " (";
    foreach ($column_list as $column) {
      if (!isset($first_insert_column)) {
        $first_insert_column = "true";
      } else {
        $insert_query .= ", ";
      };
      $insert_query .= $column;
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
      foreach ($column_list as $column) {
        if (isset($first_data_done)) {
          $insert_query .= ", ";
        } else {
          $first_data_done = 1;
        };
        if (isset($data[$column])) {
          $insert_query .= "\"" . addslashes($data[$column]) . "\"";
        } else {
          $insert_query .= "\"\"";
        };
      };
      $insert_query .= ")";
      unset($first_data_done);
      if (strlen($insert_query) > 500000) {
        unset($first_line_data_done);
        $insert_query .= " ON DUPLICATE KEY UPDATE ";
        foreach ($column_list as $column) {
          if (!isset($first_insert_column)) {
            $first_insert_column = "true";
          } else {
            $insert_query .= ", ";
          };
          $insert_query .= $column . "=VALUES(" . $column . ")";
        };
        unset($first_insert_column);
        dbquery_func($avarice_admin_connection, $insert_query, "on");
        $insert_query = "INSERT INTO " . $avarice_admin_connection['db_name'] . "." . charreplace($objectClass) . " (";
        foreach ($column_list as $column) {
          if (!isset($first_insert_column)) {
            $first_insert_column = "true";
          } else {
            $insert_query .= ", ";
          };
          $insert_query .= $column;
        };
        unset($first_insert_column);
        $insert_query .= ") VALUES ";
        unset($first_line_data_done);
      };
    };
    unset($first_line_data_done);
    $insert_query .= " ON DUPLICATE KEY UPDATE ";
    foreach ($column_list as $column) {
      if (!isset($first_insert_column)) {
        $first_insert_column = "true";
      } else {
        $insert_query .= ", ";
      };
      $insert_query .= $column . "=VALUES(" . $column . ")";
    };
    unset($first_insert_column);
    dbquery_func($avarice_admin_connection, $insert_query, "on");
  };
  $func_end_time = microtime_float();
  $func_time_taken = $func_end_time - $func_start_time;
  return $func_time_taken;
};

if (($handle = fopen($file, "r")) !== FALSE) {
  $objectclass_array = array(); $header_array = array(); $current_row = 1; $batch_counter = 1; $dbtime = 0; $num_batches = 0;
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
            $objectclass_array[$data[$oc_index]]['field_details'][charreplace($header_array[$c])] = array("is_numeric" => is_numeric($data[$c]),
                                                                                             "length"     => strlen($data[$c]));
          } else {
            if (is_numeric($data[$c]) !== $objectclass_array[$data[$oc_index]]['field_details'][charreplace($header_array[$c])]['is_numeric']) {
              $objectclass_array[$data[$oc_index]]['field_details'][charreplace($header_array[$c])]['is_numeric'] = "string";
            };
            if (strlen($data[$c]) > $objectclass_array[$data[$oc_index]]['field_details'][charreplace($header_array[$c])]['length']) {
              $objectclass_array[$data[$oc_index]]['field_details'][charreplace($header_array[$c])]['length'] = strlen($data[$c]);
            };
          };
        };
      };
    };
    if ($batch_counter == $batchSizeSpec) {
      $dbtime = $dbtime + ldap_to_db_structure($objectclass_array, $avarice_admin_module_ldap_connection);
      $dbtime = $dbtime + ldap_to_db_data($objectclass_array, $avarice_admin_module_ldap_connection);
      $objectclass_array = array();
      $num_batches++;
      $batch_counter = 1;
    } else {
      $batch_counter++;
    };
    $current_row++;
  };
  fclose($handle);
};

$dbtime = $dbtime + ldap_to_db_structure($objectclass_array, $avarice_admin_module_ldap_connection);
$dbtime = $dbtime + ldap_to_db_data($objectclass_array, $avarice_admin_module_ldap_connection);
$num_batches++;

$end_time = microtime_float();

$total_time_taken = $end_time - $start_time;
$parse_time_taken = $total_time_taken - $dbtime;

print "CSV Parsed and DBs created" . $line_break;
print "CSV file parsed in " . $parse_time_taken . " seconds" . $line_break;
print "DB tables created and data entered in " . $dbtime . " seconds" . $line_break;
print "Total time taken: " . $total_time_taken . " seconds" . $line_break;
print "Number of Rows: " . $current_row . $line_break;
print "Number of Batches: " . $num_batches . $line_break;
print "Size of Batches: " . $batchSizeSpec . $line_break;

?>
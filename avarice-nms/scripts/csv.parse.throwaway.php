<?php

include_once("../include/config.php");

$start_time = microtime_float();

if (empty($argv[1]) or !is_file($argv[1])) {
  exit("\nYou must provide a file to parse: php csv.parse.php \\path\\to\\file.csv\n");
} else {
  $file = $argv[1];
};

if (($handle = fopen($file, "r")) !== FALSE) {
  $objectclass_array = array();
  $header_array      = array();
  $current_row       = 1;
  $batch_index       = 1;
  while (($data = fgetcsv($handle)) !== FALSE) {
    $number_of_fields = count($data);
    if ($current_row == 1) {
      for ($c=0; $c < $number_of_fields; $c++) {
        $header_array[$c] = $data[$c];
      };
      $oc_index = array_search("objectClass", $header_array);
    } else {
      if (!isset(${$data[$oc_index] . "_array"})) {
        ${$data[$oc_index] . "_array"}   = array(0 => array());
        ${$data[$oc_index] . "_counter"} = 1;
        $objectclass_array[]    = $data[$oc_index];
      } else {
        ${$data[$oc_index] . "_counter"}++;
      };
      for ($c=0; $c < $number_of_fields; $c++) {
        if ($c != $oc_index and !empty($data[$c])) {
          ${$data[$oc_index] . "_array"}[${$data[$oc_index] . "_counter"}][$header_array[$c]] = $data[$c];
          if (!in_array($header_array[$c], ${$data[$oc_index] . "_array"}[0])) {
            ${$data[$oc_index] . "_array"}[0][$header_array[$c]] = array("is_numeric" => is_numeric($data[$c]),
                                                                         "length"     => strlen($data[$c]));
          } else {
            if (is_numeric($data[$c]) !== ${$data[$oc_index] . "_array"}[0][$header_array[$c]]['is_numeric']) {
              ${$data[$oc_index] . "_array"}[0][$header_array[$c]]['is_numeric'] = "string";
            };
            if (strlen($data[$c]) > ${$data[$oc_index] . "_array"}[0][$header_array[$c]]['length']) {
              ${$data[$oc_index] . "_array"}[0][$header_array[$c]]['length'] = strlen($data[$c]);
            };
          };
        };
      };
      if ($batch_index == 500) {
        
        foreach ($objectclass_array as $objectClass) {
          $table_exists_result = dbquery_func($avarice_admin_connection, "SHOW TABLES LIKE '" . str_replace("-", "_", $objectClass) . "'");
          if (mysql_num_rows($table_exists_result) == 0) {
            $create_table_query = "CREATE TABLE avarice_nms." . str_replace("-", "_", $objectClass) . " (";
            foreach (${$objectClass . "_array"}[0] as $key => $varray) {
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
            $fields_exist_details = array();
            while ($field_row = mysql_fetch_assoc($table_exists_result)) {
              $fields_exist_details[$field_row['Field']] = array("Type"    => $field_row['Type'],
                                                                 "Null"    => $field_row['Null'],
                                                                 "Key"     => $field_row['Key'],
                                                                 "Default" => $field_row['Default'],
                                                                 "Extra"   => $field_row['Extra']);
            };
            $additional_fields = array_diff(array_keys(${$objectClass . "_array"}[0]), array_keys($fields_exist_details));
            if (count($additional_fields) > 0) {
              $add_query = "ALTER TABLE " . $objectClass . " ";
              foreach ($additional_fields as $new_field) {
                if (!isset($first_add)) {
                  $first_add = "true";
                } else {
                  $add_query .= ", ";
                };
                ${$objectClass . "_array"}[0][$new_field]['is_numeric']
                $add_query .= "ADD `" . str_replace("-", "_", $new_field) . "` ";
                if (${$objectClass . "_array"}[0][$new_field]['is_numeric'] != 1)
                  $add_query .= "VARCHAR";
                } else {
                  $add_query .= "INT";
                };
                $add_query .= "( " . ${$objectClass . "_array"}[0][$new_field]['length'] . " ) NULL";
              };
              dbquery_func($avarice_admin_connection, $add_query);
            };
          //CHANGE `whenCreated` `whenCreated` VARCHAR( 173 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL
            $update_table_query = "ALTER TABLE " . str_replace("-", "_", $objectClass) . " ";
            foreach (${$objectClass . "_array"}[0] as $key => $varray) {
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
          };
          foreach (${$objectClass . "_array"} as $key => $data) {
            if ($key != 0) {
              $insert_query = "INSERT INTO avarice_nms." . str_replace("-", "_", $objectClass) . " SET ";
              foreach ($data as $key => $value) {
                if (isset(${$objectClass . "_first_data_done"})) {
                  $insert_query .= ", ";
                } else {
                  ${$objectClass . "_first_data_done"} = 1;
                };
                $insert_query .= str_replace("-", "_", $key) . " = \"" . $value . "\"";
              };
              dbquery_func($avarice_admin_connection, $insert_query);
            };
          };
        };
        $objectclass_array = array();
        unset(${$objectClass . "_first_column_done"}, ${$objectClass . "_first_data_done"});
        
      };
    };
    $current_row++;
    $batch_index++;
  };
  fclose($handle);
};

$parse_time = microtime_float();

$end_time = microtime_float();
$parse_time_taken = $parse_time - $start_time;
$db_creation_time = $end_time - $parse_time;
$total_time_taken = $end_time - $start_time;
print "CSV Parsed and DBs created\n";
print "CSV file parsed in " . $parse_time_taken . " seconds\n";
print "DB tables created and data entered in " . $db_creation_time . " seconds\n";
print "Total time taken: " . $total_time_taken . " seconds\n";

?>
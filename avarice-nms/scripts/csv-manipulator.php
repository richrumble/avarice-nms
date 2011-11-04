<?php

function throw_error($etype, $data = NULL) {
  $gen_message  = "\n";
  $gen_message .= "USAGE: php csv-manipulator.php [-options] \\path\\to\\file1.csv [\\path\\to\\file2.csv]\n";
  $gen_message .= "\t-s:\t\tSort given file(s)\n";
  $gen_message .= "\t-c:\t\tCompare given files\n";
  $gen_message .= "\t-f:\t\tFile Based. Writes results to files. (Default)\n";
  $gen_message .= "\t-d:\t\tDB Based. Writes results to DB.\n";
  $gen_message .= "\t-db:[dbname]:\tDB Based. Connects to MySQL DB and results are inserted into tables. Default: avarice_nms\n";
  $gen_message .= "\t-ho:[dbhost]:\tFQDN of MySQL DB host. Default: localhost\n";
  $gen_message .= "\t-po:[dbport]:\tTCP Port to talk to MySQL DB. Default: 3306\n";
  $gen_message .= "\t-un:[username]:\tMySQL DB user (needs table create / delete access on given MySQL DB. Default: root\n";
  $gen_message .= "\t-pw:[password]:\tMySQL DB password. Default: NULL\n";
  if ($etype == "no params") {
    $message = "No parameters given.\n";
  } else if ($etype == "invalid param") {
    $message = "\"" . $data . "\" is not a valid parameter.\n";
  } else if ($etype == "no file") {
    $message = "Cannot access \"" . $data . "\".\n";
  } else if ($etype == "file create") {
    $message = "Could not open or create " . $data . ".\n";
  } else if ($etype == "file open") {
    $message = "Could not open " . $data . ".\n";
  };
  exit("\n" . $message . $gen_message, 1);
};

function csv_sort_fb($filenames) {
  foreach ($filenames as $filename) {
    if (($handle = fopen($filename, "r")) !== FALSE) {
      $header_array = array(); $row = 1;
      while (($data = fgetcsv($handle)) !== FALSE) {
        $temp_string = "";
        if ($row == 1) {
          $header_array = $data;
          asort($header_array);
          if (($handle_out = fopen(substr($filename, 0, -4) . "-sorted.csv", "w")) === FALSE) {
            throw_error("file create", substr($filename, 0, -4) . "-sorted.csv");
          };
          $row++;
        };
        foreach ($header_array as $key => $discard) {
          $temp_string .= "\"" . $data[$key] . "\",";
        };
        fwrite($handle_out, substr($temp_string, 0, -1) . "\n");
      };
      fclose($handle_out);
    } else {
      throw_error("file open", $filename);
    };
    fclose($handle);
  };
};

function csv_sort_db($filenames, $dbconnection) {
  foreach ($filenames as $filename) {
    if (($handle = fopen($filename, "r")) !== FALSE) {
      $header_array = array(); $row = 1;
      while (($data = fgetcsv($handle)) !== FALSE) {
        $temp_string = "";
        if ($row == 1) {
          $header_array = $data;
          asort($header_array);
          if (($handle_out = fopen(substr($filename, 0, -4) . "-sorted.csv", "w")) === FALSE) {
            throw_error("file create", substr($filename, 0, -4) . "-sorted.csv");
          };
          $row++;
        };
        foreach ($header_array as $key => $discard) {
          $temp_string .= "\"" . $data[$key] . "\",";
        };
        fwrite($handle_out, substr($temp_string, 0, -1) . "\n");
      };
      fclose($handle_out);
    } else {
      throw_error("file open", $filename);
    };
    fclose($handle);
  };
};

function dbquery_func($connection_info, $query, $debug) {
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

$params = array("s" => "sort",
                "c" => "compare",
                "f" => "file",
                "d" => "db");

$dbparams = array("db" => "db_name",
                  "dt" => "db_type",
                  "ho" => "db_host",
                  "po" => "db_port",
                  "un" => "username",
                  "pw" => "password");

$dbconnection = array("db_type"  => "mysql",
                      "db_host"  => "localhost",
                      "db_port"  => "3306",
                      "db_name"  => "avarice_nms",
                      "username" => "root",
                      "password" => "");

if (count($argv) == 1) {
  throw_error("no params");
} else {
  $files = array();
  $gparams = array();
  foreach ($argv as $key => $value) {
    if ($key != 0) {
      if ($value[0] != "-") {
        $files[] = $value;
      } else {
        if ($value[3] != ":") {
          foreach(str_split(substr($value, 1)) as $char) {
            if (in_array($char, array_keys($params))) {
              $gparams[] = $params[$char];
            } else {
              throw_error("invalid param", $char);
            };
          };
        } else {
          if (in_array(substr($value, 1, 2), array_keys($dbparams))) {
            $dbconnection[$dbparams[substr($value, 1, 2)]] = substr($value, 4);
          } else {
              throw_error("invalid param", substr($value, 1, 2));
            };
          };
        };
      };
    };
  };
};

foreach ($files as $file) {
  if (!is_file($file)) {
    throw_error("no file", $file);
  };
};

if (in_array("sort", $gparams)) {
  if (!in_array("db", $gparams)) {
    csv_sort_fb($files);
  } else {
    csv_sort_db($files, $dbconnection);
  };
};

if (in_array("compare", $funcs)) {
  if (in_array("sort", $funcs)) {
    $file1 = file(substr($argv[2], 0, -4) . "-sorted.csv");
    $file2 = file(substr($argv[3], 0, -4) . "-sorted.csv");
  } else {
    $file1 = file($argv[2]);
    $file1 = file($argv[3]);
  };
  
  if (!in_array("db", $gparams)) {
    $matches = array_intersect($file1, $file2);
    $diffs   = array_merge(array_diff($file1, $file2), array_diff($file2, $file1));
  };
  
  if (($handle = fopen("csv-compare.matches.csv", "w")) !== FALSE) {
    foreach ($matches as $line) {
      fwrite($handle, $line);
    };
  };
  fclose($handle);
  if (($handle = fopen("csv-compare.diffs.csv", "w")) !== FALSE) {
    fwrite($handle, $file1[0]);
    foreach ($diffs as $line) {
      fwrite($handle, $line);
    };
  };
  fclose($handle);
};

?>
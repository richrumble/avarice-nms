<?php

if (empty($argv[1])) {
  exit("You must provide a function: php csv-manipulator.php -sc \\path\\to\\file1.csv \\path\\to\\file2.csv\n  Available functions s = sort, c = compare\n");
} else {
  $funcs = array();
  if (strpos($argv[1], "s") !== FALSE or strpos($argv[1], "S") !== FALSE) {
    $funcs[] = "sort";
  };
  if (strpos($argv[1], "c") !== FALSE or strpos($argv[1], "C") !== FALSE) {
    $funcs[] = "compare";
  };
};

if (empty($argv[2]) or !is_file($argv[2])) {
  exit($argv[2] . " is not a file: php csv-manipulator.php -sc \\path\\to\\file1.csv \\path\\to\\file2.csv\n  Available functions s = sort, c = compare\n");
};

if (!empty($argv[3]) and !is_file($argv[3])) {
  exit($argv[3] . " is not a file: php csv-manipulator.php -sc \\path\\to\\file1.csv \\path\\to\\file2.csv\n  Available functions s = sort, c = compare\n");
} else if (!empty($argv[3]) and is_file($argv[3])){
  $filename2 = $argv[3];
  $output_filename2 = substr($argv[3], 0, -4) . "-sorted.csv";
};

function csv_sort($filename) {
  if (($handle = fopen($filename, "r")) !== FALSE) {
    $header_array = array(); $row = 1;
    while (($data = fgetcsv($handle)) !== FALSE) {
      $temp_string = "";
      if ($row == 1) {
        $header_array = $data;
        asort($header_array);
        if (($handle_out = fopen(substr($filename, 0, -4) . "-sorted.csv", "w")) === FALSE) {
          exit("Could not open or create " . substr($filename, 0, -4) . "-sorted.csv" . ".\n");
        };
      };
      foreach ($header_array as $key => $discard) {
        $temp_string .= "\"" . $data[$key] . "\",";
      };
      fwrite($handle_out, substr($temp_string, 0, -1) . "\n");
      $row++;
    };
    fclose($handle_out);
  } else {
    exit("Could not open " . $filename . ".\n");
  };
  fclose($handle);
};

if (in_array("sort", $funcs)) {
  for ($x=2; $x<count($argv); $x++) {
    if (is_file($argv[$x])) {
      csv_sort($argv[$x]);
    };
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
  
  $matches = array_intersect($file1, $file2);
  $diffs   = array_merge(array_diff($file1, $file2), array_diff($file2, $file1));
  
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
<?php

if (empty($argv[1]) or !is_file($argv[1])) {
  exit("You must provide a file to sort: php csv-sorter.php \\path\\to\\file.csv \\path\\to\\output.csv\n");
} else {
  $filename = $argv[1];
};

if (empty($argv[2])) {
  exit("You must provide an output file: php csv-sorter.php \\path\\to\\file.csv \\path\\to\\output.csv\n");
} else {
  $output_filename = $argv[2];
};

if (($handle = fopen($filename, "r")) !== FALSE) {
  $header_array = array(); $row = 1;
  while (($data = fgetcsv($handle)) !== FALSE) {
    $temp_string = "";
    if ($row == 1) {
      $header_array = $data;
      asort($header_array);
      if (($handle_out = fopen($output_filename, "w")) === FALSE) {
        exit("Could not open or create " . $output_filename . ".\n");
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

?>
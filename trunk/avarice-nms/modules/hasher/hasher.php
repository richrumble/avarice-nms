<?php

function printusage() {
  print "
Usage: hasher.php -p:[path] -x:[comma seperated list of file extensions to exclude, optional] -o:[output file, optional] -h:[semicolon seperated list of hashes to perform, optional]

  Unless otherwise specified, the algorithms used are:
    crc32b
    md5
    sha1
    sha256

  Available algorithms:";
  foreach (hash_algos() as $algo) {
    print "
    " . $algo;
  };
  exit;
};

function dircrawl($dir, $hash_array, $extension_exclusions) {
  $dir = rtrim($dir, "\\/");
  $return = "";
  if ($dh = opendir($dir)) {
    while (($file = readdir($dh)) !== false) {
      if ($file != "." and $file != "..") {
        if (filetype($dir . "\\" . $file) == "dir") {
          $return .= dircrawl($dir . "\\"  . $file, $hash_array, $extension_exclusions);
        } else if (filetype($dir . "\\"  . $file) == "file") {
          if (!in_array(pathinfo($dir . "\\"  . $file, PATHINFO_EXTENSION), $extension_exclusions)) {
            $hash_output = "";
            foreach ($hash_array as $hash_type) {
              $hash_output .= ",\"" . hash_file($hash_type, $dir . "\\"  . $file) . "\"";
            };
            $return .= "\"" . dirname($dir . "\\"  . $file) . "\",\"" . basename($dir . "\\"  . $file) . "\",\"" . filesize($dir . "\\"  . $file) . "\"" . $hash_output . "\n";
          };
        };
      };
    };
  };
  return $return;
};

if ($argc == 1) {
  printusage();
};

for ($x=1; $x<$argc; $x++) {
  if ($argv[$x][1] == "p") {
    $path = substr($argv[$x], 3);
  } else if ($argv[$x][1] == "x") {
    $extension_exclusions = explode(",", substr($argv[$x], 3));
    foreach ($extension_exclusions as $key => $ext) {
      $extension_exclusions[$key] = trim($ext, ".*");
    };
  } else if ($argv[$x][1] == "h") {
    $hash_array = explode(";", substr($argv[$x], 3));
    foreach ($hash_array as $key => $ext) {
      $hash_array[$key] = trim($ext, ".*");
    };
  } else if ($argv[$x][1] == "o") {
    $output_file = substr($argv[$x], 3);
  };
};

if (empty($path)) {
  printusage();
};
if (empty($extension_exclusions)) {
  $extension_exclusions = array();
};
if (empty($hash_array)) {
  $hash_array = array("crc32b",
                      "md5",
                      "sha1",
                      "sha256");
};

$output = "\"Path\",\"FileName\",\"Size\"";
foreach ($hash_array as $hash_type) {
  $output .= ",\"" . $hash_type . "\"";
};
$output .= "\n";

if (is_file($path)) {
  $hash_output = "";
  foreach ($hash_array as $hash_type) {
    $hash_output .= ",\"" . hash_file($hash_type, $path) . "\"";
  };
  $output .= "\"" . dirname($path) . "\",\"" . basename($path) . "\",\"" . filesize($path) . "\"" . $hash_output . "\n";
} else if (is_dir($path)) {
  $output .= dircrawl($path, $hash_array, $extension_exclusions);
} else {
  print "\n" . $path . " is not an existing Path or Filename\n";
  printusage();
};

if (isset($output_file)) {
  file_put_contents($output_file, $output);
  print "\nOutput Written to " . $output_file . "\n";
} else {
  print $output . "\n";
};

?>
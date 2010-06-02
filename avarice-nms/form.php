<?php
$form_data = $_POST;
function find ($string, $array = array ()) {
  foreach ($array as $key => $value) {
    unset ($array[$key]);
    if (strpos(strtolower($value), $string) !== false) {
      $array[$key] = $value;
    };
  };
  return $array;
};
if (isset($form_data['machines'])) {
  $machines_array = explode("\n", $form_data['machines']);
  $datadump = "";
  foreach ($machines_array as $value) {
    $value = trim($value);
    if (!empty($value)) {
      exec("nslookup " . $value, $output);
      if (count($output) != 3) {
        $x = count($output) - 3;
        $y = count($output) - 2;
        $line = substr($output[$y], strrpos($output[$y], " ") + 1) . ", " . substr($output[$x], strrpos($output[$x], " ") + 1);
        exec("ping -n 1 -w 1 " . $value, $output, $result);
        if ($result == 0) {
          $line .= ", up
";
        } else {
          $line .= ", down
";
        };
      } else {
        $line = $value . " does not exist";
      };
      $line = str_replace(array("\n", "\r\n"), "", $line);
      $datadump .= $line . "\n";
      unset($nslookup_output, $output, $result, $line);
    };
  };
  $result_array = find(strtolower($form_data['item']), explode("\n", $datadump));
  if (empty($result_array)) {
    print $form_data['item'] . " not found";
  } else {
    print "Results:<br />";
    foreach ($result_array as $value) {
      print $value . "<br />";
    };
  };
  print "<hr />";
};
print "
       <form method=\"post\" action=\"form.php\">
        Machines (one per line):<br />
        <textarea rows=\"5\" cols=\"50\" name=\"machines\">"; if (isset($form_data['machines'])) { print $form_data['machines']; }; print "</textarea><br />
        Data Block (read only):<br />
        <textarea rows=\"5\" cols=\"50\" readonly>"; if (isset($datadump)) { print $datadump; }; print "</textarea><br />
        Search for:<br />
        <input type=\"text\" name=\"item\""; if (isset($form_data['item'])) { print " value=\"" . $form_data['item'] . "\""; }; print " /><br />
        <input type=\"submit\" value=\"Search\" />
       </form>
       <p />
";

?>
<?php
$form_data = $_POST;
function find ($string, $array = array ()) {
  foreach ($array as $key => $value) {
    unset ($array[$key]);
    if (strpos(strtolower($value), $string) !== false) {
      $array[$key] = $value;
    };
  };
  if (!empty($array)) {
    return $array;
  } else {
    return FALSE;
  };
};
if (isset($form_data['machines'], $form_data['ossec'])) {
  $machines_array = explode("\n", $form_data['machines']);
  $datadump = ""; $result_array = array();
  foreach ($machines_array as $value) {
    $value = trim($value);
    if (!empty($value)) {
      exec("nslookup " . $value, $output);
      if (count($output) != 3) {
        $x = count($output) - 3;
        $y = count($output) - 2;
        $ip = substr($output[$y], strrpos($output[$y], " ") + 1);
        $hn = substr($output[$x], strrpos($output[$x], " ") + 1);
        $line = $ip . ", " . $hn;
        exec("ping -n 1 -w 1 " . $ip, $output, $result);
        if ($result == 0) {
          $line .= ", up
";
        } else {
          $line .= ", down
";
        };
        if (find(strtolower($hn), explode("\n", $form_data['ossec'])) or find(strtolower($ip), explode("\n", $form_data['ossec']))) {
          $result_array[] = $value;
        };
      } else {
        $line = $value . " does not exist";
      };
      $line = str_replace(array("\n", "\r\n"), "", $line);
      $datadump .= $line . "\n";
      unset($nslookup_output, $output, $result, $line, $ip, $hn);
    };
  };
  if (empty($result_array)) {
    print "Nothing already exists";
  } else {
    print "Repeats:<br />";
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
        Parsed Machines(read only):<br />
        <textarea rows=\"5\" cols=\"50\" readonly>"; if (isset($datadump)) { print $datadump; }; print "</textarea><br />
        Already in OSSEC:<br />
        <textarea rows=\"5\" cols=\"50\" name=\"ossec\">"; if (isset($form_data['ossec'])) { print $form_data['ossec']; }; print "</textarea><br />
        <input type=\"submit\" value=\"Search\" />
       </form>
       <p />
";

?>
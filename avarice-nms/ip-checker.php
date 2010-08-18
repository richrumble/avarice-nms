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
print "
		<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
        <html>
          <head>
		    <title>IP and Name checker</title>
			<link type=\"text/css\" rel=\"stylesheet\" href=\"ip-check.css\">
		  </head>
            <body>	 
              <div id=\"left\">
               <form method=\"post\" action=\"ip-checker.php\">
                <label>Machines (one per line):</label><br />
                <textarea rows=\"5\" cols=\"50\" name=\"machines\">"; if (isset($form_data['machines'])) { print $form_data['machines']; }; print "</textarea><br />
                <br />
				<label>Copy OSSEC DB list here:</label><br />
                <textarea rows=\"5\" cols=\"50\" name=\"ossec\">"; if (isset($form_data['ossec'])) { print $form_data['ossec']; }; print "</textarea><br />
                <input type=\"submit\" value=\"Search\" />
               </form>
		     </div>
";

if (isset($form_data['machines'], $form_data['ossec'])) {
  $machines_array = explode("\n", $form_data['machines']);
  $datadump = ""; $repeats_array = array(); $uniques_array = array();
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
          $repeats_array[] = $line;
        } else {
          $uniques_array[] = $line;
        };
      } else {
        $line = $value . " does not exist";
      };
      $line = str_replace(array("\n", "\r\n"), "", $line);
      $datadump .= $line . "\n";
      unset($nslookup_output, $output, $result, $line, $ip, $hn);
    };
  };
  print " <div id=\"right\">
          Repeats:<br />
          <textarea rows=\"5\" cols=\"50\"  id=\"repeats\" name=\"repeats\">";
  if (empty($repeats_array)) {
    print "Nothing already exists";
  } else {
    foreach ($repeats_array as $value) {
      print $value . "";
    };
  };
  print "</textarea>
         <br />
         Uniques:<br />
		 <textarea rows=\"5\" cols=\"50\" id=\"uniques\" name=\"uniques\">";
  if (empty($uniques_array)) {
    print "No uniques";
  } else {
    foreach ($uniques_array as $value) {
      print $value . "";
    };
  };
  print "</textarea>
        </div>";
};
print "</body>
      </html>";
?>
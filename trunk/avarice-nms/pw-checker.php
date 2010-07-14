<?php
$form_data = $_POST;
print "
    <!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
        <html>
          <head>
        <title>PW Checker</title>
      <link type=\"text/css\" rel=\"stylesheet\" href=\"ip-check.css\">
      </head>
            <body>   
              <div id=\"left\">
               <form method=\"post\" action=\"pw-checker.php\">
                <label>Usernames (one per line):</label><br />
                <textarea rows=\"5\" cols=\"50\" name=\"usernames\">"; if (isset($form_data['usernames'])) { print $form_data['usernames']; }; print "</textarea><br />
                <br />
        <label>Copy Username,TimeStamp list here:</label><br />
                <textarea rows=\"5\" cols=\"50\" name=\"usertime\">"; if (isset($form_data['usertime'])) { print $form_data['usertime']; }; print "</textarea><br />
                <input type=\"submit\" value=\"Search\" />
               </form>
         </div>
";

if (isset($form_data['usernames'], $form_data['usertime'])) {
  $usernames_array = explode("\n", $form_data['usernames']);
  $temp_array      = explode("\n", $form_data['usertime']);
  $usertime_array  = array();
  foreach ($temp_array as $tvalue) {
    $tarray = str_getcsv($tvalue);
    $user = array_shift($tarray);
    $usertime_array[$user] = $tarray;
  };
  unset($temp_array);
  print " <div id=\"right\">
          Users Found:<br />
          <textarea rows=\"14\" cols=\"75\"  id=\"results\" name=\"results\">";
  foreach ($usernames_array as $user) {
    if (isset($usertime_array[$user])) {
      $epoch = ($usertime_array[$user][0] / 10000000) - 11644473600;
      $dtime = date('M j Y H:i:s', $epoch);
      print "\"" . $user . "\",\"" . $dtime . "\",\"" . $epoch . "\"";
      for ($x=1; $x < count($usertime_array[$user]); $x++) {
        print ",\"" . $usertime_array[$user][$x] . "\"";
      };
      print "\n";
    };
  };
  print "</textarea>
        </div>";
};
print "</body>
      </html>";
?>
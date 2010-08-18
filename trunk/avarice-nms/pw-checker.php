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
print "
    <!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
        <html>
          <head>
        <title>PW Checker</title>
      <link type=\"text/css\" rel=\"stylesheet\" href=\"ip-check.css\">
      <script type=\"text/javascript\" src=\"include/form-ajax.js\"></script>
      </head>
            <body>   
              <div id=\"left\">
               <form method=\"post\" action=\"pw-checker.php\">
                <label>Usernames (one per line):</label><br />
                <textarea rows=\"5\" cols=\"50\" name=\"usernames\">"; if (isset($form_data['usernames'])) { print $form_data['usernames']; }; print "</textarea><br />
                <input type=\"radio\" name=\"input_type\" value=\"copypaste\" onclick=\"displayBlock('copypaste'); displayNone('csvde');\"";
if (empty($form_data['input_type']) or $form_data['input_type'] == "copypaste") {
  print " checked";
};
print "> Copy \\ Paste <input type=\"radio\" name=\"input_type\" value=\"csvde\" onclick=\"displayNone('copypaste'); displayBlock('csvde');\"";
if (!empty($form_data['input_type']) and $form_data['input_type'] == "csvde") {
  print " checked";
};
print "> CSVDE<br />
                <div id=\"copypaste\" style=\"display:";
if (empty($form_data['input_type']) or $form_data['input_type'] == "copypaste") {
  print "block";
} else {
  print "none";
};
print ";\">
                 <label>Copy Username,TimeStamp list here:</label><br />
                 <textarea rows=\"5\" cols=\"50\" name=\"usertime\">"; if (isset($form_data['usertime'])) { print $form_data['usertime']; }; print "</textarea>
                </div>
                <div id=\"csvde\" style=\"display:";
if (!empty($form_data['input_type']) and $form_data['input_type'] == "csvde") {
  print "block";
} else {
  print "none";
};
print ";\">
                 <label>Location of CSVDE dump</label><br />
                 <input type=\"text\" name=\"floc\" size=\"50\"";
if (!empty($form_data['floc'])) {
  print " value=\"" . $form_data['floc'] . "\"";
};
print " />
                </div>
                <input type=\"submit\" value=\"Search\" />
               </form>
         </div>
";

if (isset($form_data['usernames'], $form_data['input_type']) and (($form_data['input_type'] == "copypaste" and isset($form_data['usertime'])) or ($form_data['input_type'] == "csvde" and isset($form_data['floc'])))) {
  $usernames_array = explode("\n", $form_data['usernames']);
  foreach($usernames_array as $key => $value) {
    $usernames_array[$key] = strtolower(trim($value));
    if (empty($usernames_array[$key])) {
      unset($usernames_array[$key]);
    };
  };
  $usertime_array  = array(); $output = "";
  if ($form_data['input_type'] == "copypaste") {
    $temp_array      = explode("\n", $form_data['usertime']);
    foreach ($temp_array as $tvalue) {
      $tarray = str_getcsv($tvalue);
      $user = strtolower(trim(array_shift($tarray)));
      $usertime_array[$user] = $tarray;
    };
    unset($temp_array);
    foreach ($usernames_array as $user) {
      if (isset($usertime_array[$user])) {
        if ($usertime_array[$user][0] != "0") {
          $epoch = ($usertime_array[$user][0] / 10000000) - 11644473600;
          $dtime = date('M j Y H:i:s', $epoch);
        } else {
          $epoch = "NA";
          $dtime = "NA";
        };
        $output .= "\"" . $user . "\",\"" . $dtime . "\",\"" . $epoch . "\"";
        for ($x=1; $x < count($usertime_array[$user]); $x++) {
          $output .= ",\"" . $usertime_array[$user][$x] . "\"";
        };
        $output .= "\n";
      };
    };
  } else if ($form_data['input_type'] == "csvde") {
    if (is_file($form_data['floc'])) {
      if (($fp = fopen($form_data['floc'], "r")) !== FALSE) {
        $account_control_array = array("16777216" => "TRUSTED_TO_AUTH_FOR_DELEGATION",
                                       "8388608"  => "PASSWORD_EXPIRED",
                                       "4194304"  => "DONT_REQ_PREAUTH",
                                       "2097152"  => "USE_DES_KEY_ONLY",
                                       "1048576"  => "NOT_DELEGATED",
                                       "524288"   => "TRUSTED_FOR_DELEGATION",
                                       "262144"   => "SMARTCARD_REQUIRED",
                                       "131072"   => "MNS_LOGON_ACCOUNT",
                                       "65536"    => "DONT_EXPIRE_PASSWORD",
                                       "8192"     => "SERVER_TRUST_ACCOUNT",
                                       "4096"     => "WORKSTATION_TRUST_ACCOUNT",
                                       "2048"     => "INTERDOMAIN_TRUST_ACCOUNT",
                                       "512"      => "NORMAL_ACCOUNT",
                                       "256"      => "TEMP_DUPLICATE_ACCOUNT",
                                       "128"      => "ENCRYPTED_TEXT_PWD_ALLOWED",
                                       "64"       => "PASSWD_CANT_CHANGE",
                                       "32"       => "PASSWD_NOTREQD",
                                       "16"       => "LOCKOUT",
                                       "8"        => "HOMEDIR_REQUIRED",
                                       "2"        => "ACCOUNTDISABLE",
                                       "1"        => "SCRIPT");
        while (!feof($fp)) {
          $line = fgetcsv($fp,4096,',','"');
          if (isset($userindex)) {
            if (isset($line[$userindex])) {
              if (in_array(strtolower(trim($line[$userindex])), $usernames_array)) {
                if ($line[$pwtimeindex] != "0") {
                  $epoch = ($line[$pwtimeindex] / 10000000) - 11644473600;
                  $dtime = date('M j Y H:i:s', $epoch);
                } else {
                  $epoch = "NA";
                  $dtime = "NA";
                };
                if (isset($line[$accountindex])) {
                  $account = "";
                  foreach ($account_control_array as $key => $value) {
                    if ($line[$accountindex] >= $key) {
                      $line[$accountindex] = $line[$accountindex] - $key;
                      $account .= " " . $value;
                    };
                  };
                  $account = trim($account);
                } else {
                  $account = "UNKNOWN";
                };
                $output .= "\"" . strtolower(trim($line[$userindex])) . "\",\"" . $dtime . "\",\"" . $epoch . "\",\"" . $account . "\",\"" . $line[$memberindex] . "\"\n";;
              };
            };
          } else {
            $userindex    = array_search("sAMAccountName", $line);
            $pwtimeindex  = array_search("pwdLastSet", $line);
            $memberindex  = array_search("memberOf", $line);
            $accountindex = array_search("userAccountControl", $line);
          };
        };
      } else {
        $output .= "Could not open " . $form_data['floc'] . "\n";
      };
    } else {
      $output .= $form_data['floc'] . " is not a file\n";
    };
  };
  
  print " <div id=\"right\">
          Users Found:<br />
          <textarea rows=\"14\" cols=\"75\" id=\"results\" name=\"results\" readonly>" . $output . "</textarea>
        </div>";
};
print "</body>
      </html>";
?>
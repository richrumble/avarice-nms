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
               <form method=\"post\" action=\"time-converter.php\">
                <label>Windows Timestamp:</label> <input type=\"text\" name=\"wintime\""; if (isset($form_data['wintime'])) { print " value=\"" . $form_data['wintime'] . "\""; }; print " /><br />
";

if (isset($form_data['wintime'])) {
  if (is_numeric($form_data['wintime']) and $form_data['wintime'] > 116444736000000000) {
    $epoch = ($form_data['wintime'] / 10000000) - 11644473600;
    $dtime = date('M j Y H:i:s', $epoch);
    print "
                    <label>Epoch:</label> " . $epoch . "<br />
                    <label>Date:</label> " . $dtime . "<br />
    ";
  } else {
    print "Not a valid Windows timestamp<br />";
  };
};

print "
                <input type=\"submit\" value=\"Convert\" />
               </form>
              </div>
       </body>
      </html>";
?>
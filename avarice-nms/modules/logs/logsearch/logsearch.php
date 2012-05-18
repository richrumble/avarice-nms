<?php
date_default_timezone_set('America/Indiana/Indianapolis');
$form_data = $_REQUEST;
if (empty($form_data['action'])) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<!--All attributes need to be quoted for valid xhtml-transitional markup-->
 <head>
  <title>Avarice 1.0</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <link rel="stylesheet" href="../../../css/style.css" type="text/css" />
  <link rel="icon" href="img/favicon.ico" type="img/x-icon" />
  <script type = "text/javascript" src = "jquery-1.6.1.min.js"></script>
  <script type = "text/javascript" src = "form.js"></script>
 </head>
 <body>
  <div class="header">
   <div id="head_nav">
    <ul> 
     <li><a href="index.html" class="active"><span>Home</span></a></li> <!-- class = active for the current page -->
    </ul>
   </div> <!-- End Head-Nav Div -->
   <div id="search" class="noprint">
    <form method="get" action="">
     <fieldset>
      <legend class="legend_null">Search</legend>
      <label><input id="search_input" type="text" size="30" name=""/></label>
     </fieldset>
    </form>
   </div> <!-- End Search Div -->
  </div> <!-- End Header Div -->
  <div class="left_column">
   <b class="rtop">
    <b class="r0"></b> <b class="r1"></b> <b class="r2"></b> <b class="r3"></b> <b class="r4"></b>
   </b>
   <div class="cell">
    <form class = "formtodiv" targetdiv = "results" action = "logsearch.php" method = "POST">
     <fieldset>
      <legend>Event Log Search</legend>
      <label class="creds">FQDN or IP Target(s) | (csv)</label>
       <input type="text" name="fqdn" /> <br />
      <label class="creds">Username (domain\user)</label>
       <input type="text" name="user" /> <br />
      <label class="creds">Password </label>
       <input type="password" name="pass" /> <br />
      <input type = "hidden" name = "action" value = "search" />
      <h3>Choose Log(s)</h3>
      <label>
       <input type = "radio" name = "logfile" value = "all" checked />All&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      </label>
      <label>
       <input class = "right" type = "radio" name = "logfile" value = "Application" />Application
      </label><br />
      <label>
       <input type = "radio" name = "logfile" value = "Security" />Security
      </label>
      <label>
       <input class = "right" type = "radio" name = "logfile" value = "System" />System
      </label>
      <h3>Timeframe</h3>
      <label><input type = "radio" name = "timeframe" value = "-1 hour" checked />Last Hour&nbsp;</label>
      <label><input class = "right" type = "radio" name = "timeframe" value = "-1 day" />Last Day</label><br />
      <label><input type = "radio" name = "timeframe" value = "-1 week" />Last Week</label>
      <label><input class = "right" type = "radio" name = "timeframe" value = "-1 month" />Last Month</label><br />
      <input type = "submit" value = "Search" />
     </fieldset>
    </form>
   </div> <!-- End Cell Div -->
   <b class="rbottom">
    <b class="r4"></b> <b class="r3"></b> <b class="r2"></b> <b class="r1"></b><b class="r0"></b>
   </b>
  <div class="arrow-border-btm"></div>
  <div class="arrow-btm"></div>
  </div> <!-- End Left_Column Div -->
  <div class="main">
   <b class="rtop">
    <b class="r0"></b> <b class="r1"></b> <b class="r2"></b> <b class="r3"></b> <b class="r4"></b>
   </b>
  <div class="cell">
   <div id = "results">
   </div> <!-- End Results Div -->
  </div> <!-- End Cell Div -->
    <b class="rbottom">
     <b class="r4"></b> <b class="r3"></b> <b class="r2"></b> <b class="r1"></b><b class="r0"></b>
    </b>
  </div> <!-- End Main Div -->
   <div class="footer">
    <p>Contact us and stuff
     <a href="http://jigsaw.w3.org/css-validator/check/referer"><img src="./img/w3c-css.png" alt="Valid CSS!" height="31" width="88" /></a>
     <a href="http://validator.w3.org/check?uri=referer"><img src="./img/w3c-xhtml.png" alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>
    </p>
   </div> <!-- End Footer Div -->
 </body>
</html>
      

<?php
} else if ($form_data['action'] == "search") {
	$output = "
		<h1>Results:</h1>
		<hr />";
	$output .= $form_data['fqdn'] . "<br />";
	if (!isset($form_data['fqdn'])) {
		$form_data['fqdn'] = ".";
	};
	$computers = explode(",", $form_data['fqdn']);
	
	$query = "Select * from Win32_NTLogEvent Where";
	if ($form_data['logfile'] != "all") {
		$query .= " LogFile = '" . $form_data['logfile'] . "' and";
	};
	$query .= " TimeWritten >= '" . date('YmdHis.000000-000', strtotime($form_data['timeframe'])) . "'";
	
	foreach ($computers as $computer) {
		$computer = trim($computer);
		$output .= "Computer: " . $computer . "<br />";
		if ($computer == "." or (empty($form_data['user']) and empty($form_data['pass']))) {
			$objWMIService = new COM("winmgmts:{impersonationLevel=impersonate,(Security)}//" . $computer . "\\root\\cimv2");
		} else {
			$obj = new COM('WbemScripting.SWbemLocator');
			$objWMIService = $obj->ConnectServer($strComputer, '/root/cimv2', $user, $password);
		};
		$LoggedEvents = $objWMIService->ExecQuery($query);
		foreach ($LoggedEvents as $objEvent) {
			$output .= "Category: " . $objEvent->Category . "<br />";
			$output .= "Computer Name: " . $objEvent->ComputerName . "<br />";
			$output .= "Event Code: " . $objEvent->EventCode . "<br />";
			$output .= "Log File: " . $objEvent->LogFile . "<br />";
			$output .= "Message: " . $objEvent->Message . "<br />";
			$output .= "Record Number: " . $objEvent->RecordNumber . "<br />";
			$output .= "Source Name: " . $objEvent->SourceName . "<br />";
			$output .= "Time Written: " . $objEvent->TimeWritten . "<br />";
			$output .= "Event Type: " . $objEvent->Type . "<br />";
			$output .= "User: " . $objEvent->User . "<br />";
			$output .= "<hr />";
		};
		print $query . "<br />";
	};
	print $output;
};

?>
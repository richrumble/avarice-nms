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
  <link rel="stylesheet" href="css/style.css" type="text/css" />
  <link rel="icon" href="img/favicon.ico" type="img/x-icon" />
 </head>
 <body>
  <div class="header">
   <div id="head_nav">
    <ul> 
     <li><a href="index.html" class="active"><span>Home</span></a></li> <!-- class = active for the current page -->
     <li><a href="About-Us.html" class="nav"><span>About us</span></a></li>
     <li><a href="FAQ.html" class="nav"><span>F.A.Q.</span></a></li>
     <li><a href="raw-packet.html" class="nav"><span>Raw Packets</span></a></li>
    </ul>
   </div> <!-- End Head-Nav Div -->
   <div id="search" class="noprint">
    <form method="get" action="">
     <fieldset>
      <legend class="legend_null">Search</legend>
      <label>
       <span id="search_header">
        <input id="search_input" type="text" size="30" name=""/>
       </span>
      </label>
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
	  <input type = "hidden" name = "action" value = "search" />
      <h1>Logfile</h1>
		 <input type = "radio" name = "logfile" value = "all" checked><label>All</label><br />
      <input type = "radio" name = "logfile" value = "Application"><label>Application</label><br />
      <input type = "radio" name = "logfile" value = "Security"><label>Security</label><br />
      <input type = "radio" name = "logfile" value = "System"><label>System</label><br />
      <h1>Timeframe</h1>
      <input type = "radio" name = "timeframe" value = "-1 hour" checked><label>Last Hour</label><br />
      <input type = "radio" name = "timeframe" value = "-1 day"><label>Last Day</label><br />
      <input type = "radio" name = "timeframe" value = "-1 week"><label>Last Week</label><br />
      <input type = "radio" name = "timeframe" value = "-1 month"><label>Last Month</label><br />
      <input type = "submit" value = "Search" />
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
   <h1>Main Body</h1>
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
	$query = "Select * from Win32_NTLogEvent Where";
	if (!isset($form_data['machine'])) {
		$form_data['machine'] = ".";
	};
	if ($form_data['logfile'] != "all") {
		$query .= " LogFile = '" . $form_data['logfile'] . "' and";
	};
	$query .= " TimeWritten >= '" . date('YmdHis.000000-000', strtotime($form_data['timeframe'])) . "'";
	$objWMIService = new COM("winmgmts:{impersonationLevel=impersonate,(Security)}//" . $form_data['machine'] . "\\root\\cimv2");
	$LoggedEvents = $objWMIService->ExecQuery($query);
	foreach ($LoggedEvents as $objEvent) {
		$output .= "Category: " . $objEvent->Category . "<br />";
		$output .= "Computer Name: " . $objEvent->ComputerName . "<br />";
		$output .= "Event Code: " . $objEvent->EventCode . "<br />";
		$output .= "Log File: " . $objEvent->LogFile . "<br />";
		$output .= "Message: " . htmlentities($objEvent->Message) . "<br />";
		$output .= "Record Number: " . $objEvent->RecordNumber . "<br />";
		$output .= "Source Name: " . $objEvent->SourceName . "<br />";
		$output .= "Time Written: " . $objEvent->TimeWritten . "<br />";
		$output .= "Event Type: " . $objEvent->Type . "<br />";
		$output .= "User: " . $objEvent->User . "<br />";
		$output .= "<hr />";
	};
	print $query . "<br />";
	print $output;
};

?>
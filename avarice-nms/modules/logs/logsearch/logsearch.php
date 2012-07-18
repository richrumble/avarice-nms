<?php
$wbemFlagReturnImmediately=0x10;
$wbemFlagForwardOnly=0x20;
$WbemAuthenticationLevelPktPrivacy=6;

function win_time($timestr) {
 return substr($timestr, 4, 2) . "/" . substr($timestr, 6, 2) . "/" .
 substr($timestr, 0, 4) . " " . substr($timestr, 8, 2) . ":" .
 substr($timestr, 10, 2) . ":" . substr($timestr, 12, 2) . " "; //leaving off the TimeZone offset.
 // substr($timestr, 10, 2) . ":" . substr($timestr, 12, 2) . " " . substr($timestr, -4);
};

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
     <li><a href="index.php" class="nav"><span>Home</span></a></li>
     <li><a href="inventory.php" class="nav"><span>Inventory</span></a></li>
     <li><a href="logsearch.php" class="active"><span>EVT Log</span></a></li> <!-- class = active for the current page -->
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
       <input type="text" name="fqdn" placeholder="LocalHost" /> <br />
      <label class="creds">Username (domain\user)</label>
       <input type="text" name="user" placeholder="<?PHP passthru("whoami"); ?>" /> <br />
      <label class="creds">Password </label>
       <input type="password" name="pass" /> <br />
      <input type = "hidden" name = "action" value = "search" />
      <h3>Choose Log(s)</h3>
      <label>
       <input type = "radio" name = "logfile" value = "all" checked="checked" />All&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
      <label>
        <input type = "radio" name = "timeframe" value = "1 day" checked="checked" />All&nbsp;Time
      </label><br />
      <label>
        <input type = "radio" name = "timeframe" value = "-1 hour" />Last Hour&nbsp;
      </label>
      <label>
      	<input class = "right" type = "radio" name = "timeframe" value = "-1 day" />Last Day
     </label><br />
      <label>
      	<input type = "radio" name = "timeframe" value = "-1 week" />Last Week
      </label>
      <label>
      	<input class = "right" type = "radio" name = "timeframe" value = "-1 month" />Last Month
      </label><br />
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
    <p>EVENT LOG SEARCH: Results will appear here. The default credentials being used are typically shown in the form, if nothing is shown
      assume the credentials that this program is being run under are being used and the LocalHost is the Target. You can specify multiple
      hosts on the Target(s) form input by comma separating FQDN's or IP's. Unless you input a Username <b>and</b> Password the default
      credentials will be used.
    </p>
    <p>Pagination, exporting/saving and filtering results are coming soon. Searching results can be accomplished by pressing ctrl+f like
      in your browser. Monitoring and scheduling tasks is also on the horizon.
    </p>
   </div> <!-- End Results Div -->
  </div> <!-- End Cell Div -->
    <b class="rbottom">
     <b class="r4"></b> <b class="r3"></b> <b class="r2"></b> <b class="r1"></b><b class="r0"></b>
    </b>
  </div> <!-- End Main Div -->
   <div class="footer">
    <p>Contact us and stuff
     <a href="http://jigsaw.w3.org/css-validator/check/referer"><img src="../../../img/w3c-css.png" alt="Valid CSS!" height="31" width="88" /></a>
     <a href="http://validator.w3.org/check?uri=referer"><img src="../../../img/w3c-xhtml.png" alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>
    </p>
   </div> <!-- End Footer Div -->
 </body>
</html>


<?php
} else if ($form_data['action'] == "search") {
	$output = "
	  <script type=\"text/javascript\">
      \$(document).ready(function() {
        \$(\"#results tr\").mouseover(function(){\$(this).addClass(\"over\");}).mouseout(function(){\$(this).removeClass(\"over\");});
        \$(\"#results tr:odd\").addClass(\"odd\");
        \$(\"#results tr:even\").addClass(\"even\");
      });
    </script>
		<h1>Results:</h1>
		<hr />";
	//$output .= $form_data['fqdn'] . "<br />";
	if (empty($form_data['fqdn'])) {
		$form_data['fqdn'] = ".";
	};
	$computers = explode(",", $form_data['fqdn']);
	
	$query = "Select * from Win32_NTLogEvent";
	if ($form_data['logfile'] != "all") {
		$query .= "  WHERE LogFile = '" . $form_data['logfile'] . "'";
		$whereand =" AND ";
	} else {
		$whereand =" WHERE ";
	};
	if ($form_data['timeframe'] != "1 day") {
		$query .= $whereand . " TimeWritten >= '" . date('YmdHis.000000-000', strtotime($form_data['timeframe'])) . "'";
  } else {
	  $query .= $whereand . " TimeWritten <= '" . date('YmdHis.000000-000', strtotime($form_data['timeframe'])) . "'";
	};
	
	foreach ($computers as $computer) {
		$computer = trim($computer);
		if ($computer == "." or (empty($form_data['user']) and empty($form_data['pass']))) {
			$objWMIService = new COM("winmgmts:{impersonationLevel=impersonate,(Security)}//" . $computer . "\\root\\cimv2");
		} else {
			$obj = new COM('WbemScripting.SWbemLocator');
			$obj->Security_->ImpersonationLevel=3; /* http://msdn.microsoft.com/en-us/library/windows/desktop/aa393787%28v=vs.85%29.aspx */
			$objWMIService = $obj->ConnectServer($computer, '/root/cimv2', $form_data['user'], $form_data['pass']);
		};
		$colItems = $objWMIService->ExecQuery($query);
		$counts = array (
			"Category"   => array(),
			"EventCode"  => array(),
			"LogFile"    => array(),
			"SourceName" => array(),
			"Type"       => array(),
			"User"       => array()
		);
		foreach ($colItems as $objItem) {
			foreach ($counts as $key => $value) {
				if (!in_array($objItem->$key, array_keys($value))) {
					$counts[$key][$objItem->$key] = 0;
				};
				$counts[$key][$objItem->$key]++;
			};
			$output .= "
				<div class = \"tag_" . $objItem->Category . " tag_" . $objItem->EventCode . " tag_" . $objItem->LogFile . " tag_" . $objItem->SourceName . " tag_" . $objItem->Type . " tag_" . $objItem->User . "\">
					<table id=\"results\">
					  <tbody>
						<tr>
							<th>Category:&nbsp;</th>
							<td>" . $objItem->Category . "</td>
						</tr>
						<tr>
							<th>Computer&nbsp;Name:&nbsp;</th>
							<td>" .	$objItem->ComputerName . "</td>
						</tr>
						<tr>
							<th>Event&nbsp;Code:&nbsp;</th>
							<td>" .	$objItem->EventCode . "</td>
						</tr>
						<tr>
							<th>Log&nbsp;File:&nbsp;</th>
							<td>" .	$objItem->LogFile . "</td>
						</tr>
						<tr>
							<th>Message:&nbsp;</th>
							<td>" .	str_replace(array("\r\n", "\t"),array("<br />", "&nbsp;&nbsp;&nbsp;"), $objItem->Message) . "</td>
						</tr>
						<tr>
							<th>Record&nbsp;Number:&nbsp;</th>
							<td>" .	$objItem->RecordNumber . "</td>
						</tr>
						<tr>
							<th>Source&nbsp;Name:&nbsp;</th>
							<td>" .	$objItem->SourceName . "</td>
						</tr>
						<tr>
							<th>Time&nbsp;Written:&nbsp;</th>
							<td>" .	win_time($objItem->TimeWritten) . "</td>
						</tr>
						<tr>
							<th>Event&nbsp;Type:&nbsp;</th>
							<td>" .	$objItem->Type . "</td>
						</tr>
						<tr>
							<th>User:&nbsp;</th>
							<td>" .	$objItem->User. "</td>
						</tr>
						</tbody>
					</table>
				</div>";
		};
	};
	$countshead = "
		<h1>Filter By:</h1>
		<hr />
		<div>
			<table>
			  <tbody>
				<tr>
					<th onclick=\"filterparams('category');\">Categories </th>
					<th onclick=\"filterparams('eventcode');\">EventCodes </th>
					<th onclick=\"filterparams('logfile');\">LogFiles </th>
					<th onclick=\"filterparams('sourcename');\">SourceNames </th>
					<th onclick=\"filterparams('type');\">Types </th>
					<th onclick=\"filterparams('user');\">Users</th>
				</tr>
				</tbody>
			</table>";
	foreach ($counts as $cate => $dets) {
		if ($cate != "SourceName") {
			$xlimit = 6;
		} else {
			$xlimit = 3;
		};
		$countshead .= "
						<div style=\"display: none;\" class=\"filtercat\" id=\"" . strtolower($cate) . "\">
							<table>
							  <tbody>";
		uksort($dets, 'strnatcasecmp');
		$x = 0;
		foreach ($dets as $det => $dcount) {
			$x++;
			if ($x == 1) {
				$countshead .= "
								<tr>";
			};
			$countshead .= "
									<td><input type = \"checkbox\" name = \"check_" . $cate . "\" value = \"" . $det . "\" onchange = \"filterlogs('check_" . $cate . "')\" checked></td>
									<td>" . $det . "</td>";
			if ($x == $xlimit) {
				$countshead .= "
								</tr>";
				$x = 0;
			}
		};
		$countshead .= "
		            </tbody>
							</table>
						</div>";
	};
	$countshead .= "
					</td>
				</tr>
				</tbody>
			</table>
		</div>";
	print $countshead . $output;
};

?>
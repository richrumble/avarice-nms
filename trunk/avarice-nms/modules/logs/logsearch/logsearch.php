<?php

$form_data = $_REQUEST;
if (empty($form_data['action'])) {
?>

<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
	<head>
		<title>Avarice - EventLog Search</title>
		<meta http-equiv = "content-type" content = "text/html; charset=utf-8" />
		<meta http-equiv = "CACHE-CONTROL" content = "NO-CACHE" />
		<meta http-equiv = "PRAGMA" content = "NO-CACHE" />
		<script type = "text/javascript" src = "jquery-1.6.1.min.js"></script>
		<script type = "text/javascript" src = "form.js"></script>
	</head>
	<body>
		<div>
			<form class = "formtodiv" targetdiv = "results" action = "logsearch.php" method = "POST">
				<input type = "hidden" name = "action" value = "search" />
				<table border = 1>
					<tr>
						<th>Logfile</th>
						<td>
							<input type = "radio" name = "logfile" value = "all" checked> All<br />
							<input type = "radio" name = "logfile" value = "Application"> Application<br />
							<input type = "radio" name = "logfile" value = "Security"> Security<br />
							<input type = "radio" name = "logfile" value = "System"> System<br />
						</td>
					</tr>
					<tr>
						<th>Timeframe</th>
						<td>
							<input type = "radio" name = "timeframe" value = "-1 hour" checked> Last Hour<br />
							<input type = "radio" name = "timeframe" value = "-1 day">Last Day<br />
							<input type = "radio" name = "timeframe" value = "-1 week">Last Week<br />
							<input type = "radio" name = "timeframe" value = "-1 month">Last Month<br />
						</td>
					</tr>
					<tr>
						<td colspan = 2 style = "text-align: center;">
							<input type = "submit" value = "Search" />
						
						</td>
					</tr>
				</table>
			</form>
		</div>
		<div id = "results">
		</div>
	</body>
</html>

<?php

} else if ($form_data['action'] == "search") {
	$output = "
			<h1>Results:</h1>
			<hr />";
	
	$query = "Select * from Win32_NTEventLogFile Where";
	if (!isset($form_data['machine']) {
		$form_data['machine'] = ".";
	};
	if ($form_data['logfile'] != "all") {
		$query .= " LogFileName = '" . $form_data['logfile'] . "' AND";
	};
	
	$query .= " TimeWritten >= '" . date('YmdHis.000000-000', strtotime($form_data['timefram'])) . "'";
	
	$objWMIService = new COM("winmgmts:{impersonationLevel=impersonate,(Security)}//" . $form_data['machine'] . "\\root\\cimv2");
	$LoggedEvents = $objWMIService->ExecQuery($query);
	foreach ($LoggedEvents as $objEvent) {
		$output .= "Category: " . $objEvent->Category . "<br />";
		$output .= "Computer Name: " . $objEvent->ComputerName . "<br />";
		$output .= "Event Code: " . $objEvent->EventCode . "<br />";
		$output .= "Message: " . $objEvent->Message . "<br />";
		$output .= "Record Number: " . $objEvent->RecordNumber . "<br />";
		$output .= "Source Name: " . $objEvent->SourceName . "<br />";
		$output .= "Time Written: " . $objEvent->TimeWritten . "<br />";
		$output .= "Event Type: " . $objEvent->Type . "<br />";
		$output .= "User: " . $objEvent->User . "<br />";
		$output .= "<hr />"
	};
};

?>
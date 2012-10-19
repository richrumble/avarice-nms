<?php
$batchsize     = 1000;
$displayamount = 200;

function win_time($timestr) {
 return substr($timestr, 0, 4) . "-" . substr($timestr, 4, 2) . "-" . substr($timestr, 6, 2)
  . " " . substr($timestr, 8, 2) . ":" .
 substr($timestr, 10, 2) . ":" . substr($timestr, 12, 2); //leaving off the TimeZone offset.
 // substr($timestr, 10, 2) . ":" . substr($timestr, 12, 2) . " " . substr($timestr, -4);
};

date_default_timezone_set('America/Indiana/Indianapolis');
$form_data = $_REQUEST;

$snorm = array(
	"Category"   => "Categories",
	"EventCode"  => "EventCodes",
	"LogFile"    => "Logfiles",
	"SourceName" => "SourceNames",
	"Type"       => "Types",
	"User"       => "Users"
);
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
		<form class = "formtodiv" targetdiv = "mainresults" action = "logsearch.php" method = "POST">
		 <fieldset>
			<legend>Event Log Search</legend>
			<label class="creds">FQDN or IP Target(s) | (csv)</label>
			 <input type="text" name="fqdn" placeholder="LocalHost" /> <br />
			<label class="creds">Username (domain\user)</label>
			 <input type="text" name="user" placeholder="<?PHP passthru("whoami"); ?>" /> <br />
			<label class="creds">Password </label>
			 <input type="password" name="pass" /> <br />
			<input type = "hidden" name = "action" value = "form2" /><br />
			<input type = "submit" value = "Connect" />
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
	 <div id = "mainresults">
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
} else if ($form_data['action'] == "form2") {
	if (is_file('loglitedb.sqlite3')) {
		unlink('loglitedb.sqlite3');
	};
	
	try {$dbh = new PDO('sqlite:loglitedb.sqlite3');
		$query = "CREATE TABLE Events (pkID INTEGER PRIMARY KEY, ComputerName VARCHAR (256), Message TEXT, RecordNumber INT, TimeWritten TEXT, ";
		$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$dbh->exec("PRAGMA journal_mode = MEMORY; PRAGMA temp_store = MEMORY; PRAGMA synchronous = OFF");
		foreach ($snorm as $key => $value) {
			$dbh->exec("CREATE TABLE " . $value . " (pkID INTEGER PRIMARY KEY, " . $key . " VARCHAR(128) UNIQUE)");
			$query .= $key . "ID INT, ";
		};
		$query = substr($query, 0, -2) . ")";
		#print $query;
		$dbh->exec($query);
	} catch(PDOException $e) {
		echo $e->getMessage();
	};
	if (empty($form_data['fqdn'])) {
		$form_data['fqdn'] = ".";
	};
	$computers = explode(",", $form_data['fqdn']);
	$query = "Select * from Win32_NTLogEvent";
	foreach ($computers as $computer) {
		$computer = trim($computer);
		if ($computer == "." or (empty($form_data['user']) and empty($form_data['pass']))) {
			$objWMIService = new COM("winmgmts:{impersonationLevel=impersonate,authenticationLevel=pktPrivacy,(Security)}!//" . $computer . "\\root\\cimv2");
		} else {
			$obj = new COM('WbemScripting.SWbemLocator');
			$obj->Security_->ImpersonationLevel = 3; /* http://msdn.microsoft.com/en-us/library/windows/desktop/aa393787%28v=vs.85%29.aspx */
			$obj->Security_->AuthenticationLevel = 6; /* http://msdn.microsoft.com/en-us/library/windows/desktop/ms695984%28v=vs.85%29.aspx */
			$objWMIService = $obj->ConnectServer($computer, '/root/cimv2', $form_data['user'], $form_data['pass']);
		};
		$colItems = $objWMIService->ExecQuery($query,'WQL',48);
		$x = 0;
		$total = 0;
		$query = "BEGIN TRANSACTION; ";
		foreach ($colItems as $objItem) {
			foreach ($snorm as $key => $value) {
				if ($x == 0) {
					${"norm_query_" . $value} = "BEGIN TRANSACTION;";
				};
				${"norm_query_" . $value} .= "
				INSERT OR IGNORE INTO " . $value . " (" . $key . ") VALUES ('" . $objItem->$key . "'); ";
				if ($x >= $batchsize) {
					${"norm_query_" . $value} .= " COMMIT;";
					$dbh->exec(${"norm_query_" . $value});
					${"norm_query_" . $value} = "BEGIN TRANSACTION;";
				};
			};
			$query .= "INSERT INTO Events (CategoryID, ComputerName, EventCodeID, LogfileID, Message, RecordNumber, SourceNameID, TimeWritten, TypeID, UserID) VALUES
					(
						(
							SELECT
								pkID
							FROM
								Categories
							WHERE
								Category = '" . $objItem->Category . "'
						),
						'" . $objItem->ComputerName . "',
						(
							SELECT
								pkID
							FROM
								EventCodes
							WHERE
								EventCode = '" . $objItem->EventCode . "'
						),
						(
							SELECT
								pkID
							FROM
								Logfiles
							WHERE
								Logfile = '" . $objItem->LogFile . "'
						),
						'" . str_replace(array("'"), "", $objItem->Message) . "',
						'" . $objItem->RecordNumber . "',
						(
							SELECT
								pkID
							FROM
								SourceNames
							WHERE
								SourceName = '" . $objItem->SourceName . "'
						),
						'" . win_time($objItem->TimeWritten) . "',
						(
							SELECT
								pkID
							FROM
								Types
							WHERE
								Type = '" . $objItem->Type . "'
						),
						(
							SELECT
								pkID
							FROM
								Users
							WHERE
								User = '" . $objItem->User . "'
						)
					); ";
			if ($x < $batchsize) {
				$x++;
			} else {
				$total +=$x;
				$x = 0;
				$dbh->exec($query . " COMMIT;");
				$query = "
					BEGIN TRANSACTION; ";
			};

		};
		foreach ($snorm as $key => $value) {
			$dbh->exec(${"norm_query_" . $value} . " COMMIT;");
		};
		$dbh->exec($query . " COMMIT;");
	};
?>
	<div id="filters">
		<form class = "formtodiv" targetdiv = "results" action = "logsearch.php" method = "POST">
			<fieldset>
				<input type = "hidden" name = "action" value = "search" />
				Search: <input size = 30 type = "text" id = "searchString" name = "searchString" onkeyup="$(this).closest('form').submit();"/>
<?php
	try {$dbh = new PDO('sqlite:loglitedb.sqlite3');
		foreach ($snorm as $key => $value) {
			print "
					<table class=\"filtertable\">
						<tr>
							<th>Choose " . $key . "(s)</th>
						</tr>
						<tr>
							<td>
								<select multiple=\"multiple\" name=\"filter_" . $key . "[]\" size=\"3\">
									<option value=\"all\" selected>All</option>";
			foreach ($dbh->query("SELECT * FROM " . $value . " ORDER BY " . $key . " COLLATE NOCASE ASC") as $row) {
				print "
									<option value=\"" . $row['pkID'] . "\">" . $row[$key] . "</option>";
			};
			print "
								</select>
							</td>
						</tr>
					</table>";
		};
	} catch(PDOException $e) {
		echo $e->getMessage();
	};
?>
				<br />
				<input type = "hidden" name = "action" value = "search" />
				<input type = "submit" value = "Search" />
			</fieldset>
		</form>
	</div>
	<div id="results">
	</div>
<?php
} else if ($form_data['action'] == "search") {
	try {$dbh = new PDO('sqlite:loglitedb.sqlite3');
		if (!isset($form_data['pageNum'])) {
			$form_data['pageNum'] = 1;
		};
		$query = "SELECT Events.ComputerName, Events.Message, Events.RecordNumber, Events.TimeWritten, ";
		$qwhere = "";
		$join = "";
		foreach ($snorm as $key => $value) {
			$query .= $value . "." . $key . ", ";
			$join .= "JOIN " . $value . " ON Events." . $key . "ID = " . $value . ".pkID ";
			if (!in_array("all", $form_data["filter_" . $key])) {
				$qwhere .= "AND " . $key . "ID IN (";
				foreach($form_data["filter_" . $key] as $filter) {
					$qwhere .= $filter . ", ";
				};
				$qwhere = substr($qwhere, 0, -2) . ") ";
			};
		};
		if (!empty($form_data['searchString'])) {
			$qwhere = "AND Events.Message LIKE '%" . $form_data['searchString'] . "%' " . $qwhere;
		};
		if ($qwhere != "") {
			$qwhere = "WHERE " . substr($qwhere, 3);
		};
		$offset = ($form_data['pageNum'] - 1) * $displayamount;
		$countquery = "SELECT COUNT(*) FROM Events " . $qwhere;
		$sth = $dbh->prepare($countquery);
		$sth->execute();
		$result = $sth->fetchAll();
		$totalCount = $result[0][0];
		$query = substr($query, 0, -2) . " FROM Events " . $join . $qwhere . " ORDER BY TimeWritten DESC LIMIT " . $offset . "," . $displayamount;
		$sth = $dbh->prepare($query);
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		$requesttoform = "";
		foreach ($form_data as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $vv) {
					$requesttoform .=  "
						<input type=\"hidden\" name=\"" . $key . "[]\" value=\"" . $vv . "\" />";
				};
			} else {
				if ($key != "pageNum") {
					$requesttoform .=  "
						<input type=\"hidden\" name=\"" . $key . "\" value=\"" . $value . "\" />";
				};
			};
		};
		$pagination = "<< ";
		for ($x = ($form_data['pageNum'] - 2); $x <= ($form_data['pageNum'] + 2); $x++) {
			if ($x > 0 and $x <= (ceil($totalCount / $displayamount))) {
				if ($x != $form_data['pageNum']) {
					$pagination .= "
						<form style=\"display: inline;\" class = \"formtodiv\" targetdiv = \"results\" action = \"logsearch.php\" method = \"POST\">" . $requesttoform . "
							<input type=\"hidden\" name=\"pageNum\" value=\"" . $x . "\" />
							<a href=\"#\" onclick=\"\$(this).closest('form').submit()\">" . $x . "</a>
						</form>";
				} else {
					$pagination .= $x . " ";
				};
			};
		};
		$pagination = $pagination . ">>";
		print "
				<div>
					" . ((($form_data['pageNum'] - 1) * $displayamount) + 1) . " to " . ((($form_data['pageNum'] - 1) * $displayamount) + number_format(count($result))) . " of " . number_format($totalCount) . " Events. " . $pagination . "
				</div>";
		foreach ($result as $row) {
			print "
				<hr>
				<table>";
			foreach ($row as $key => $value) {
				if ($key != "pkID") {
					print "
						<tr>
							<td>" . $key . ":</td>
							<td>" . $value . "</td>
						</tr>";
				};
			};
			print "
				</table>";
		};
	} catch(PDOException $e) {
		echo $e->getMessage();
	};
};

?>
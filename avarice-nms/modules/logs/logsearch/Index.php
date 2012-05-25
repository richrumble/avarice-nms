<?php
$wbemFlagReturnImmediately=0x10;
$wbemFlagForwardOnly=0x20;
$WbemAuthenticationLevelPktPrivacy=6;

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
  <script type = "text/javascript" src = "highlight.js"></script>
 <!-- <script type = "text/javascript" src = "placeholder-support.js"></script> -->
 </head>
 <body>
  <div class="header">
   <div id="head_nav">
    <ul> 
     <li><a href="index.php" class="active"><span>Home</span></a></li> <!-- class = active for the current page -->
     <li><a href="inventory.php" class="nav"><span>Inventory</span></a></li>
     <li><a href="logsearch.php" class="nav"><span>EVT Log</span></a></li>
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
   <form class = "formtodiv" targetdiv = "results" action = "index.php" method = "POST">
     <fieldset>
      <legend>Computer Information</legend>
      <label class="creds">FQDN or IP Target(s) | (csv)</label>
       <input type="text" name="fqdn" placeholder="LocalHost" /> <br />
      <label class="creds">Username (domain\user)</label>
       <input type="text" name="user" placeholder="<?PHP passthru("whoami"); ?>" /> <br />
      <label class="creds">Password </label>
       <input type="password" name="pass" /> <br />
      <input type = "hidden" name = "action" value = "search" />
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
   	<p>Results will appear here</p>
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
	if (empty($form_data['fqdn'])) {
		$form_data['fqdn'] = ".";
	};
	$computers = explode(",", $form_data['fqdn']);
	$query = "Select * from Win32_UserAccount Where LocalAccount = True";
	
	foreach ($computers as $computer) {
		$computer = trim($computer);
		//$output .= "Computer: " . $computer . "<br />";
		if ($computer == "." or (empty($form_data['user']) and empty($form_data['pass']))) {
			$objWMIService = new COM("winmgmts:{impersonationLevel=impersonate,(Security)}//" . $computer . "\\root\\cimv2");
		} else {
			$obj = new COM('WbemScripting.SWbemLocator');
			$obj->Security_->ImpersonationLevel=3;
			$objWMIService = $obj->ConnectServer($computer, '/root/cimv2', $form_data['user'], $form_data['pass']);
		};
		$colItems=$objWMIService->ExecQuery($query);
		foreach ($colItems as $objItem) {
      $output .= "AccountType " . $objItem->AccountType . "<br />";
      $output .= "Caption " . $objItem->Caption . "<br />";
      $output .= "Description " . $objItem->Description . "<br />";
      $output .= "Disabled " . $objItem->Disabled . "<br />";
      $output .= "Domain " . $objItem->Domain . "<br />";
      $output .= "FullName " . $objItem->FullName . "<br />";
      $output .= "InstallDate " . $objItem->InstallDate . "<br />";
      $output .= "LocalAccount " . $objItem->LocalAccount . "<br />";
      $output .= "Lockout " . $objItem->Lockout . "<br />";
      $output .= "Name " . $objItem->Name . "<br />";
      $output .= "PasswordChangeable " . $objItem->PasswordChangeable . "<br />";
      $output .= "PasswordExpires " . $objItem->PasswordExpires . "<br />";
      $output .= "PasswordRequired " . $objItem->PasswordRequired . "<br />";
      $output .= "SID " . $objItem->SID . "<br />";
      $output .= "SIDType " . $objItem->SIDType . "<br />";
      $output .= "Status " . $objItem->Status . "<br />";
			$output .= "<hr />";
    }
	};
	print $output;
};
		
// define('HISTORY_LIST',34); //put any path that leads to the history FOLDER (C:\Users\aneagles\AppData\Local\Microsoft\Windows\History)
// $ITEM_NAME=0;
// $ITEM_DATE=2;
// $objShell = new COM("Shell.Application");
// $objHistory = $objShell->NameSpace(HISTORY_LIST);
// $objHistoryFolder = $objHistory->Self;
// Echo "<br />"."Location of History ";
// Echo $objHistoryFolder->Path;
// foreach ($objHistory->Items as $objPeriod) {
//   Echo "<br />".$objPeriod->Name;
//   Echo str_repeat("=",strlen($objPeriod->Name));
//   if ($objPeriod->IsFolder) {
//     $objSiteFolder=$objPeriod->GetFolder;
//     foreach ($objSiteFolder->Items as $objSite) {
//       Echo "<br />".$objSite->Name;
//       Echo str_repeat("-",strlen($objSite->Name));
//       if ($objSite->IsFolder) {
//         $objPageFolder = $objSite->GetFolder;
//         foreach ($objPageFolder->Items as $objPage) {
//           $strURL = $objPageFolder->GetDetailsOf($objPage,$ITEM_NAME);
//           Echo "<br />"."URL: ".$strURL;
//           $strDateVisited = $objPageFolder->GetDetailsOf($objPage,$ITEM_DATE);
//           Echo "Date Visited: " . $strDateVisited;
//         }
//       } 
//     }
//   } 
// }

?>
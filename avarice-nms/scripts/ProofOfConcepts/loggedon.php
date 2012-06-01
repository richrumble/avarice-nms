<?PHP
//header('Content-Type: text/plain');
//$wbemFlagReturnImmediately=0x10;
//$wbemFlagForwardOnly=0x20;
//$WbemAuthenticationLevelPktPrivacy=6;
$arrComputers = array(".");
$get_sid  = new Variant("",VT_BSTR );
$get_user = new Variant("",VT_BSTR );
$get_domain = new Variant("",VT_BSTR);

// $user = $argv[1];
// $password = $argv[2];
// $arrComputers = explode($argv[3], ",");

foreach ($arrComputers as $strComputer) {
	$obj = new COM('WbemScripting.SWbemLocator');
	$wmi = $obj->ConnectServer($strComputer, '/root/cimv2');   //Use the second method below if no CLI computer is given
	//$wmi = new COM('winmgmts:{impersonationLevel=impersonate}!//' . $strComputer . '/root/cimv2');      //Use local computer
	$colItems = $wmi->ExecQuery("SELECT * FROM Win32_Process WHERE Name = 'explorer.exe'");
    foreach ($colItems as $colItem) {
      if ( $colItem != null ){
      echo "Explorer is running as " . "\r\n";
      $colItem->GetOwner ($get_user, $get_domain);
      echo "    " . $get_domain . "\\" . $get_user . "\r\n";
     } else {
       echo "explorer is not running" . "\r\n";
    }
  }
};
?>

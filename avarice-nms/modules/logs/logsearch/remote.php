<?PHP
//header('Content-Type: text/plain');
$wbemFlagReturnImmediately=0x10;
$wbemFlagForwardOnly=0x20;
$WbemAuthenticationLevelPktPrivacy=6;

$get_sid  = new Variant("",VT_BSTR );
$get_user = new Variant("",VT_BSTR );
$get_domain = new Variant("",VT_BSTR);

$user = 'administrator';
$password = "D#L#T#thispass#";
$arrComputers=array('192.168.1.10');

foreach ($arrComputers as $strComputer) {
	$obj = new COM('WbemScripting.SWbemLocator');
	$obj->Security_->ImpersonationLevel=3;
	$wmi = $obj->ConnectServer($strComputer, '/root/cimv2', $user, $password);   //Use the second method below if no CLI computer is given
	//$wmi = new COM('winmgmts:{impersonationLevel=impersonate}!//' . $strComputer . '/root/cimv2');      //Use local computer
	$colItems = $wmi->ExecQuery("SELECT * FROM Win32_Bios");
    foreach ($colItems as $colItem) {
    Echo "BuildNumber: " . $colItem->BuildNumber . "<br />";
    Echo "Caption: " . $colItem->Caption . "<br />";
    Echo "CodeSet: " . $colItem->CodeSet . "<br />";
    Echo "CurrentLanguage: " . $colItem->CurrentLanguage . "<br />";
    Echo "Description: " . $colItem->Description . "<br />";
    Echo "IdentificationCode: " . $colItem->IdentificationCode . "<br />";
    }
  $get_sid  = new Variant("",VT_BSTR );
  $strUser = new Variant("",VT_BSTR);
  $strDomain = new Variant("",VT_BSTR);    
  $colItems = $wmi->ExecQuery("SELECT * FROM Win32_Process WHERE Name = 'explorer.exe'");
    foreach ($colItems as $colItem) {
      $re_turn = $colItem->GetOwner($strUser, $strDomain);
      $re_turn = $colItem->GetOwnerSID($get_sid);
      if ($re_turn != 0) {
        Echo"Could not get owner info for process " . $colItem->Name . "Error = " . $re_turn . "<br />";
      } else {
        Echo "Process " . $colItem->Name . " is owned by " . $strDomain . "\\" .$strUser . " and SID = " .$get_sid . "<br />";
      }
    }
};
?>
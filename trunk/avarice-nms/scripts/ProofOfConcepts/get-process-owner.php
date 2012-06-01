<?PHP
header('Content-Type: text/plain');
$strComputer = ".";
$get_sid  = new Variant("",VT_BSTR );
$strUser = new Variant("",VT_BSTR );
$strDomain = new Variant("",VT_BSTR);

$objWMIService = new COM('winmgmts:{impersonationLevel=impersonate}!//' . $strComputer . '/root/cimv2');
$colProcesses = $objWMIService->ExecQuery("SELECT * FROM Win32_Process");
foreach ($colProcesses as $objProcess) {
  $re_turn = $objProcess->GetOwner($strUser, $strDomain);
  if ($re_turn != 0) {
    Echo"Could not get owner info for process " . $objProcess->Name . "\n" . "Error = " . $re_turn . "\r\n";
  } else {
    Echo"Process " . $objProcess->Name . " is owned by " . $strDomain . "\\" .$strUser . "." . "\r\n";
  } 
}

?>


<?PHP
//This function converts "20120201211425.631101-300" and rearranges it from
// 2012 02 01 21 14 25 .631101-300
// Year  M  D  H  m  s  micro secs | -300 is offset (EST timezone in this case)
function win_time($timestr) {
 return substr($timestr, 4, 2) . "/" . substr($timestr, 6, 2) . "/" .
substr($timestr, 0, 4) . " " . substr($timestr, 8, 2) . ":" .
substr($timestr, 10, 2) . ":" . substr($timestr, 12, 2) . " " .
substr($timestr, -4);
// OUTPUT: 02/01/2012 21:14:25 -300  (M/D/Y H:m:s TZ)
};

$strComputer = ".";
$wmi = new COM("winmgmts:\\\\" . $strComputer . "\\root\\cimv2");
$wmiEvent = $wmi->ExecNotificationQuery("SELECT * FROM __InstanceOperationEvent " . " Within .1 WHERE TargetInstance ISA 'Win32_Process'", "WQL");

$get_user = new Variant("",VT_BSTR );
$get_domain = new Variant("",VT_BSTR);

Echo "Monitoring Processes ...\n";
while(true) {
  $evt = $wmiEvent->NextEvent;
  switch ($evt->Path_->Class)
  {
    case "__InstanceCreationEvent":
    $error = $evt->TargetInstance->GetOwner($get_user);
    if ($error!=0) {
    	Echo "Could not get Owner Info - Error: " . $error;
    } else {
    	 $evtCreated = win_time($evt->TargetInstance->CreationDate);
       $evt->TargetInstance->GetOwner($get_user, $get_domain);
       Echo "New Process Created  : " . $evtCreated . "\n";
       Echo "New Process Name     : " . $evt->TargetInstance->Name . "\n";
       Echo "Process Owner        : " . $get_domain . "\\" . $get_user;
       Echo "\n" . "New Process Path     : " . $evt->TargetInstance->ExecutablePath . "\n";
       Echo "New Process ID       : " . $evt->TargetInstance->ProcessId . "\n";
       Echo "Parent Process ID    : " . $evt->TargetInstance->ParentProcessId . "\n";
       Echo "New Process Priority : " . $evt->TargetInstance->Priority . "\n";
       break;
    }
    case "__InstanceDeletionEvent":
       Echo "Process Terminated   : " . $evt->TargetInstance->ProcessId . "\n";
       Echo "Process Name         : " . $evt->TargetInstance->Name . "\n";
       break;
       Echo "-------------------------------------\n";
    }
  }

?>



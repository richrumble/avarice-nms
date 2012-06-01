<?PHP
$strComputer = ".";
$wmi = new COM("winmgmts:\\\\" . $strComputer . "\\root\\cimv2");
$wmiEvent = $wmi->ExecNotificationQuery("SELECT * FROM __InstanceOperationEvent Within 1 Where TargetInstance ISA 'Win32_LogicalDisk'");
$i=1;
$i++;
while ($i!=0){
  $usb = $wmiEvent->NextEvent;
  if ($usb->TargetInstance->DriveType==2) {
    switch ($usb->Path_->Class) {
      case "__InstanceCreationEvent":
        Echo "Drive " . $usb->TargetInstance->DeviceId . " has been added.\n";
        break;
      case "__InstanceDeletionEvent":
        Echo "Drive " . $usb->TargetInstance->DeviceId . " has been removed.\n";
        break;
    }
  }
};
?>
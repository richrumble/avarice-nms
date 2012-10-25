<?PHP
// Use double quotes in the query lines so that single quotes can also be used
// Quick WQL note: Query strings for LIKE "LIKE %Win32%" will find Win32_something
// "IS NOT NULL" or "IS NULL" are correct but not IS NOT "NULL", don't quote NULL.
// ---- Win_time turns some windows time stamps into more common formats
function win_time($timestr) {
 return substr($timestr, 4, 2) . "/" . substr($timestr, 6, 2) . "/" .
 substr($timestr, 0, 4) . " " . substr($timestr, 8, 2) . ":" .
 substr($timestr, 10, 2) . ":" . substr($timestr, 12, 2) . " " .
 substr($timestr, -4);
};
// ---- Connection Settings
$strComputer=".";
$objWMIService=new COM("winmgmts:{impersonationLevel=impersonate,authenticationLevel=pktPrivacy,(Security)}!//" . $strComputer . "\\root\\cimv2");

// ---- Records Current Bias and when DaylightSavings will take effect.
//$colItems=$objWMIService->ExecQuery("SELECT * FROM Win32_TimeZone");
//  foreach ($colItems as $objItem) {
//    Echo "Bias: " . $objItem->Bias . "\n";
//    Echo "Caption: " . $objItem->Caption . "\n";
//    Echo "DaylightBias: " . $objItem->DaylightBias . "\n";
//    Echo "DaylightDay: " . $objItem->DaylightDay . "\n";
//    Echo "DaylightDayOfWeek: " . $objItem->DaylightDayOfWeek . "\n";
//    Echo "DaylightHour: " . $objItem->DaylightHour . "\n";
//    Echo "DaylightMinute: " . $objItem->DaylightMinute . "\n";
//    Echo "DaylightMonth: " . $objItem->DaylightMonth . "\n";
//    Echo "DaylightName: " . $objItem->DaylightName . "\n";
//    Echo "DaylightSecond: " . $objItem->DaylightSecond . "\n";
//    Echo "DaylightYear: " . $objItem->DaylightYear . "\n";
//    Echo "Description: " . $objItem->Description . "\n";
//    Echo "SettingID: " . $objItem->SettingID . "\n";
//    Echo "StandardBias: " . $objItem->StandardBias . "\n";
//    Echo "StandardDay: " . $objItem->StandardDay . "\n";
//    Echo "StandardDayOfWeek: " . $objItem->StandardDayOfWeek . "\n";
//    Echo "StandardHour: " . $objItem->StandardHour . "\n";
//    Echo "StandardMinute: " . $objItem->StandardMinute . "\n";
//    Echo "StandardMonth: " . $objItem->StandardMonth . "\n";
//    Echo "StandardName: " . $objItem->StandardName . "\n";
//    Echo "StandardSecond: " . $objItem->StandardSecond . "\n";
//    Echo "StandardYear: " . $objItem->StandardYear . "\n";
//  };
// ---- Get the current time in UTC on this machine.
//  $colItems=$objWMIService->ExecQuery("SELECT * FROM Win32_UTCTime");
//  foreach ($colItems as $objItem) {
//    Echo "Day: " . $objItem->Day . "\n";
//    Echo "DayOfWeek: " . $objItem->DayOfWeek . "\n";
//    Echo "Hour: " . $objItem->Hour . "\n";
//    Echo "Milliseconds: " . $objItem->Milliseconds . "\n";
//    Echo "Minute: " . $objItem->Minute . "\n";
//    Echo "Month: " . $objItem->Month . "\n";
//    Echo "Quarter: " . $objItem->Quarter . "\n";
//    Echo "Second: " . $objItem->Second . "\n";
//    Echo "WeekInMonth: " . $objItem->WeekInMonth . "\n";
//    Echo "Year: " . $objItem->Year . "\n";
//  };

// ---- Query Event Logs
// Need a stored placeholder so we can pick up where we left off:
// ("Win32_NTLogEvent where logfile='security' and RecordNumer >'12345'")
// This would be best after a reboot or the exe exiting and running again

print "Category, CategoryString, ComputerName, EventCode,
         EventIdentifier, EventType, Logfile, Message, RecordNumber,
         SourceName, TimeGenerated, TimeWritten, Type, User, InsertionStrings" . "\n";
$objects_array = array("Category"       => "string",
                     "CategoryString"   => "string",
                     "ComputerName"     => "string",
                     "EventCode"        => "string",
                     "EventIdentifier"  => "string",
                     "EventType"        => "string",
                     "Logfile"          => "string",
                     "Message"          => "string",
                     "RecordNumber"     => "string",
                     "SourceName"       => "string",
                     "TimeGenerated"    => "time",
                     "TimeWritten"      => "time",
                     "Type"             => "string",
                     "User"             => "string",
                     "InsertionStrings" => "array");

$colItems=$objWMIService->ExecQuery("SELECT * FROM Win32_NTLogEvent"); //This query can also be used
 foreach ($objWMIService->instancesof("Win32_NTLogEvent") as $objItem) {
   $line = "";
   foreach ($objects_array as $disp_obj => $disp_type) {
     if ($disp_type == "string") {
       $line .= "\"" . trim($objItem->$disp_obj) . "\",";
     } else if ($disp_type == "time") {
       $line .= "\"" . win_time($objItem->$disp_obj) . "\",";
     } else if ($disp_type == "array") {
       $line .= "\"";
       foreach ($objItem->$disp_obj as $string) {
         $line .= $string . ",";
       };
       $line = substr($line, 0, -1) . "\",";
     };
   };
   print substr($line, 0, -1) . "\n";
 };

?>
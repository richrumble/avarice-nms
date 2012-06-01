<?PHP
//---------------- Create a logfile with the current time for the first run ----------------\\
header("Content-Type: text/plain");
$arrComputers=array(".");
// Windows records Event Logs in UTC
// This function will convert event logs based on the local machines current Time Settings
// Skip this function/conversion to store them without any "bias"
function win_time($timestr) {
 return substr($timestr, 4, 2) . "/" . substr($timestr, 6, 2) . "/" .
 substr($timestr, 0, 4) . " " . substr($timestr, 8, 2) . ":" .
 substr($timestr, 10, 2) . ":" . substr($timestr, 12, 2) . " " .
 substr($timestr, -4);
};
  print "Category, CategoryString, ComputerName, EventCode,
         EventIdentifier, EventType, Logfile, Message, RecordNumber,
         SourceName, TimeGenerated, TimeWritten, Type, User, InsertionStrings" . "\n";
  $objects_array = array("Category"     => "string",
                     "CategoryString"   => "string",
                     "ComputerName"     => "string",
                     "Data"             => "array",
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
                    "InsertionStrings"  => "array");

foreach ($arrComputers as $strComputer) {
  $obj = new COM('winmgmts:{impersonationLevel=impersonate,(Security)}//' . $strComputer . '/root/cimv2');
  foreach ($obj->instancesof('Win32_NTLogEvent') as $objItem) {
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

//---------------- Record the event's current Offset/Bias information ----------------\\
  $colItems=$objWMIService->ExecQuery("SELECT * FROM Win32_TimeZone");
  foreach ($colItems as $objItem)
  {
    Echo "Bias: " . $objItem->Bias;
    Echo "Caption: " . $objItem->Caption;
    Echo "DaylightBias: " . $objItem->DaylightBias;
    Echo "DaylightDay: " . $objItem->DaylightDay;
    Echo "DaylightDayOfWeek: " . $objItem->DaylightDayOfWeek;
    Echo "DaylightHour: " . $objItem->DaylightHour;
    Echo "DaylightMinute: " . $objItem->DaylightMinute;
    Echo "DaylightMonth: " . $objItem->DaylightMonth;
    Echo "DaylightName: " . $objItem->DaylightName;
    Echo "DaylightSecond: " . $objItem->DaylightSecond;
    Echo "DaylightYear: " . $objItem->DaylightYear;
    Echo "Description: " . $objItem->Description;
    Echo "SettingID: " . $objItem->SettingID;
    Echo "StandardBias: " . $objItem->StandardBias;
    Echo "StandardDay: " . $objItem->StandardDay;
    Echo "StandardDayOfWeek: " . $objItem->StandardDayOfWeek;
    Echo "StandardHour: " . $objItem->StandardHour;
    Echo "StandardMinute: " . $objItem->StandardMinute;
    Echo "StandardMonth: " . $objItem->StandardMonth;
    Echo "StandardName: " . $objItem->StandardName;
    Echo "StandardSecond: " . $objItem->StandardSecond;
    Echo "StandardYear: " . $objItem->StandardYear;
  };
//---------------- Get the current time in UTC on this machine ----------------\\
  $colItems=$objWMIService->ExecQuery("SELECT * FROM Win32_UTCTime");
  foreach ($colItems as $objItem) {
    Echo "Day: " . $objItem->Day . "\n";                   
    Echo "DayOfWeek: " . $objItem->DayOfWeek . "\n";       
    Echo "Hour: " . $objItem->Hour . "\n";                 
    Echo "Minute: " . $objItem->Minute . "\n";             
    Echo "Month: " . $objItem->Month . "\n";               
    Echo "Quarter: " . $objItem->Quarter . "\n";           
    Echo "Second: " . $objItem->Second . "\n";             
    Echo "WeekInMonth: " . $objItem->WeekInMonth . "\n";   
    Echo "Year: " . $objItem->Year . "\n";                 
  };

?>

<?php

// ---- Sets URL where data will be submitted
$avarice_webservice_URL = "http://domain.com/webservice";

// --- Sets up the $objects array
$objects_array = array(
	"Category"         => "string",
	"CategoryString"   => "string",
	"ComputerName"     => "string",
//	"Data"             => "array", // --- "Data" needs to be converted from Decimal to Ascii (using CHR http://php.net/manual/en/function.chr.php)
// --- This "Data" will need a place in the DB as well, we'll add this support soon.
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
	"InsertionStrings" => "array"
);

// ---- Instantiates returndata array
$return_data = array();

// ---- Win_time turns some windows time stamps into more common formats
function win_time($timestr) {
	return substr($timestr, 4, 2) . "/" . substr($timestr, 6, 2) . "/" .
	substr($timestr, 0, 4) . " " . substr($timestr, 8, 2) . ":" .
	substr($timestr, 10, 2) . ":" . substr($timestr, 12, 2) . " " .
	substr($timestr, -4);
};

// ---- Connection Settings
$strComputer = ".";
$objWMIService = new COM("winmgmts:{impersonationLevel=impersonate,(Security)}//" . $strComputer . "\\root\\cimv2");

// ---- Enumerate Local Users
$return_data['local users'] = array();
$colItems = $objWMIService->ExecQuery("Select * from Win32_UserAccount Where LocalAccount = True");
foreach ($colItems as $objItem) {
	$return_data['local users'][] = array(
		"AccountType"        => $objItem->AccountType,
		"Caption"            => $objItem->Caption,
		"Description"        => $objItem->Description,
		"Disabled"           => $objItem->Disabled,
		"Domain"             => $objItem->Domain,
		"FullName"           => $objItem->FullName,
		"LocalAccount"       => $objItem->LocalAccount,
		"Lockout"            => $objItem->Lockout,
		"Name"               => $objItem->Name,
		"PasswordChangeable" => $objItem->PasswordChangeable,
		"PasswordExpires"    => $objItem->PasswordExpires,
		"PasswordRequired"   => $objItem->PasswordRequired,
		"SID"                => $objItem->SID,
		"SIDType"            => $objItem->SIDType,
		"Status"             => $objItem->Status
	);
};

// ---- Enumerate Local Groups
$return_data['local groups'] = array();
$colItems = $objWMIService->ExecQuery("SELECT * FROM Win32_Group Where LocalAccount = True");
foreach ($colItems as $objItem) {
	$return_data['local groups'][] = array(
		"Caption"      => $objItem->Caption,
		"Description"  => $objItem->Description,
		"Domain"       => $objItem->Domain,
		"LocalAccount" => $objItem->LocalAccount,
		"Name"         => $objItem->Name,
		"SID"          => $objItem->SID,
		"SIDType"      => $objItem->SIDType,
		"Status"       => $objItem->Status,
	);
};

// ---- Enumerate Local Group Membership
$return_data['group membership'] = array();
$colItems = $objWMIService->ExecQuery("SELECT * FROM Win32_GroupUser");
foreach ($colItems as $objItem) {
	$return_data['group membership'][] = array(
		"GroupComponent" => $objItem->GroupComponent,
		"PartComponent"  => $objItem->PartComponent
	);
};

// ---- Records Current Bias and when DaylightSavings will take effect.
$colItems = $objWMIService->ExecQuery("SELECT * FROM Win32_TimeZone");
foreach ($colItems as $objItem) {
	$return_data['timezone'] = array(
		"Bias"              => $objItem->Bias,
		"Caption"           => $objItem->Caption,
		"DaylightBias"      => $objItem->DaylightBias,
		"DaylightDay"       => $objItem->DaylightDay,
		"DaylightDayOfWeek" => $objItem->DaylightDayOfWeek,
		"DaylightHour"      => $objItem->DaylightHour,
		"DaylightMinute"    => $objItem->DaylightMinute,
		"DaylightMonth"     => $objItem->DaylightMonth,
		"DaylightName"      => $objItem->DaylightName,
		"DaylightSecond"    => $objItem->DaylightSecond,
		"DaylightYear"      => $objItem->DaylightYear,
		"Description"       => $objItem->Description,
		"SettingID"         => $objItem->SettingID,
		"StandardBias"      => $objItem->StandardBias,
		"StandardDay"       => $objItem->StandardDay,
		"StandardDayOfWeek" => $objItem->StandardDayOfWeek,
		"StandardHour"      => $objItem->StandardHour,
		"StandardMinute"    => $objItem->StandardMinute,
		"StandardMonth"     => $objItem->StandardMonth,
		"StandardName"      => $objItem->StandardName,
		"StandardSecond"    => $objItem->StandardSecond,
		"StandardYear"      => $objItem->StandardYear
	);
};

// ---- Enumerate Asset Data
$colItems = $objWMIService->ExecQuery("SELECT * FROM Win32_ComputerSystem"); //ARRAYS ARE NOT COMMA SEPERATED YET
foreach ($colItems as $objItem) {
	$strRoles = array($objItem->Roles);
	$strInitialLoadInfo = array($objItem->InitialLoadInfo);
	$strOEMLogoBitmap = array($objItem->OEMLogoBitmap);
	$strOEMStringArray = array($objItem->OEMStringArray);
	$strPowerManagementCapabilities = array($objItem->PowerManagementCapabilities);
	$strSupportContactDescription = array($objItem->SupportContactDescription);
	$strSystemStartupOptions = array($objItem->SystemStartupOptions);
	$return_data['asset'] = array(
		"AdminPasswordStatus"         => $objItem->AdminPasswordStatus,
		"AutomaticManagedPagefile"    => $objItem->AutomaticManagedPagefile,
		"AutomaticResetBootOption"    => $objItem->AutomaticResetBootOption,
		"AutomaticResetCapability"    => $objItem->AutomaticResetCapability,
		"BootOptionOnLimit"           => $objItem->BootOptionOnLimit,
		"BootOptionOnWatchDog"        => $objItem->BootOptionOnWatchDog,
		"BootROMSupported"            => $objItem->BootROMSupported,
		"BootupState"                 => $objItem->BootupState,
		"Caption"                     => $objItem->Caption,
		"ChassisBootupState"          => $objItem->ChassisBootupState,
		"CreationClassName"           => $objItem->CreationClassName,
		"CurrentTimeZone"             => $objItem->CurrentTimeZone,
		"DaylightInEffect"            => $objItem->DaylightInEffect,
		"Description"                 => $objItem->Description,
		"DNSHostName"                 => $objItem->DNSHostName,
		"Domain"                      => $objItem->Domain,
		"DomainRole"                  => $objItem->DomainRole,
		"EnableDaylightSavingsTime"   => $objItem->EnableDaylightSavingsTime,
		"FrontPanelResetStatus"       => $objItem->FrontPanelResetStatus,
		"InfraredSupported"           => $objItem->InfraredSupported,
		"InitialLoadInfo"             => $strInitialLoadInfo,
		"KeyboardPasswordStatus"      => $objItem->KeyboardPasswordStatus,
		"LastLoadInfo"                => $objItem->LastLoadInfo,
		"Manufacturer"                => $objItem->Manufacturer,
		"Model"                       => $objItem->Model,
		"Name"                        => $objItem->Name,
		"NameFormat"                  => $objItem->NameFormat,
		"NetworkServerModeEnabled"    => $objItem->NetworkServerModeEnabled,
		"NumberOfLogicalProcessors"   => $objItem->NumberOfLogicalProcessors,
		"NumberOfProcessors"          => $objItem->NumberOfProcessors,
		"OEMLogoBitmap"               => $strOEMLogoBitmap,
		"OEMStringArray"              => $strOEMStringArray,
		"PartOfDomain"                => $objItem->PartOfDomain,
		"PauseAfterReset"             => $objItem->PauseAfterReset,
		"PCSystemType"                => $objItem->PCSystemType,
		"PowerManagementCapabilities" => $strPowerManagementCapabilities,
		"PowerManagementSupported"    => $objItem->PowerManagementSupported,
		"PowerOnPasswordStatus"       => $objItem->PowerOnPasswordStatus,
		"PowerState"                  => $objItem->PowerState,
		"PowerSupplyState"            => $objItem->PowerSupplyState,
		"PrimaryOwnerContact"         => $objItem->PrimaryOwnerContact,
		"PrimaryOwnerName"            => $objItem->PrimaryOwnerName,
		"ResetCapability"             => $objItem->ResetCapability,
		"ResetCount"                  => $objItem->ResetCount,
		"ResetLimit"                  => $objItem->ResetLimit,
		"Roles"                       => $strRoles,
		"Status"                      => $objItem->Status,
		"SupportContactDescription"   => $strSupportContactDescription,
		"SystemStartupDelay"          => $objItem->SystemStartupDelay,
		"SystemStartupOptions"        => $strSystemStartupOptions,
		"SystemStartupSetting"        => $objItem->SystemStartupSetting,
		"SystemType"                  => $objItem->SystemType,
		"ThermalState"                => $objItem->ThermalState,
		"TotalPhysicalMemory"         => $objItem->TotalPhysicalMemory,
		"UserName"                    => $objItem->UserName,
		"WakeUpType"                  => $objItem->WakeUpType,
		"Workgroup"                   => $objItem->Workgroup
	);
};

// ---- Pull Event Log Data
$return_data['events'] = array();
$x = 0;
$colItems = $objWMIService->ExecQuery("SELECT * FROM Win32_NTLogEvent");
foreach ($objWMIService->instancesof("Win32_NTLogEvent") as $objItem) {
	$return_data['events'][$x] = array();
	foreach ($objects_array as $disp_obj => $disp_type) { 
		if ($disp_type == "string") {
			$return_data['events'][$x][$disp_obj] = trim($objItem->$disp_obj); 
		} else if ($disp_type == "time") {
			$return_data['events'][$x][$disp_obj] = win_time($objItem->$disp_obj);
		} else if ($disp_type == "array") {
			if ($objItem->$disp_obj != NULL) {
				$return_data['events'][$x][$disp_obj] = array();
				foreach ($objItem->$disp_obj as $string) {
					$return_data['events'][$x][$disp_obj][] = $string;
				};
			};
		};
	};
	$x++;
};

print_r($return_data);

?>
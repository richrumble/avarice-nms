<?php
header('Content-Type: text/plain');
define ('STR_COMPUTER', '.');
$obj = new COM('winmgmts:{impersonationLevel=impersonate}//' . STR_COMPUTER . '/root/SecurityCenter');
$name_spaces = array();
$colClasses=$obj->SubclassesOf;
foreach ($colClasses as $objClass) {
  foreach ($objClass->Qualifiers_() as $objClassQualifier) {
//    if (strtolower($objClassQualifier->Name) == "static") {
      $name_spaces[$objClass->Path_->Class] = array();
//    };
  };
};

uksort($name_spaces, 'strnatcasecmp');

Echo "<WMI_Output>\r\n";
foreach ($name_spaces as $class => $value) {
  Echo " <Namespace Name=\"" . $class . "\">\r\n";
  $obj = new COM('winmgmts://' . STR_COMPUTER . '\root\SecurityCenter' . ':' . $class);
  foreach ($obj->Properties_() as $objClassProperty) {
    Echo "  <Property Name=\"" . $objClassProperty->Name . "\">\r\n";
    if ($objClassProperty->IsArray == true) {
      Echo "   <Qualifier Name=\"IsArray\">true</Qualifier>\r\n";
    };
    foreach ($objClassProperty->Qualifiers_ as $objClassPropQual) {
      if (@variant_get_type($objClassPropQual->Value) == (VT_VARIANT + VT_ARRAY)){
        Echo "   <Qualifier Name=\"" . $objClassPropQual->Name . "\">";
        $objClassPropQualVal = "";
        foreach ($objClassPropQual->Value as $value) {
          $objClassPropQualVal .= $value . ", ";
        };
        Echo substr($objClassPropQualVal, 0, -2) . "</Qualifier>\r\n";
      } else {
        Echo "   <Qualifier Name=\"" . $objClassPropQual->Name . "\">" . $objClassPropQual->Value . "</Qualifier>\r\n";
      };
    };
    Echo "  </Property>\r\n";
  };
  Echo " </Namespace>\r\n";
};
Echo "</WMI_Output>";
?>
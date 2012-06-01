<?PHP
$objSchemaComputer = new COM("LDAP://schema/computer");
foreach ($objSchemaComputer->MandatoryProperties as $strAttribute){
       Echo $strAttribute . "<br>";
}
Echo "\r\n" . "Optional (May-Contain) attributes";
foreach ($objSchemaComputer->OptionalProperties as $strAttribute){
 Echo $strAttribute . "<br>";
}
?>


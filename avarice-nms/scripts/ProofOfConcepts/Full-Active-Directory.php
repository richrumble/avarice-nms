<?php
//header("Content-Type: text/plain");
$objConnection = new COM ("ADODB.Connection");
//$objConnection->Open("Provider=ADsDSOObject;user id=domain\\jdoe;password=somel0ngP@ss!!;");     //!!!PASSWORD AND LDAP DATA IN PLAIN_TEXT DO NOT USE!!!!!
$objConnection->Open("Provider=ADsDSOObject;user id=ops\\jdoe;password=somel0ngP@ss!!;encrypt password=true;");     //ENCRYPTS PASSWORD!!
$objCommand = new COM("ADODB.Command");
$objCommand->ActiveConnection = $objConnection ;

//$objCommand->Properties["Asynchronous"] = True;     //SETTING TO TRUE DIDN'T MAKE A DIFFERENCE TO ME...
$objCommand->Properties["Cache Results"] = false;     //DEFAULT = True (http://msdn.microsoft.com/en-us/library/aa746471%28v=vs.85%29.aspx)
$objCommand->Properties["Chase Referrals"] = False;     //DEFAULT = False
//$objCommand->Properties["Column Names Only"] = True;     //DEFAULT = False
//$objCommand->Properties["Deref Aliases"] = False;     //DEFAULT = False
$objCommand->Properties["Page Size"] = 999;     //GET AROUND AD'S 1000 RECORD (DEFAULT)LIMIT AND PAGENATES RESULTS
$objCommand->Properties["SearchScope"] = 2;     //DEFAULT = ADS_SCOPE_SUBTREE(aka 2) (http://msdn.microsoft.com/en-us/library/aa772286%28v=vs.85%29.aspx)
//$objCommand->Properties["Size Limit"] = 0;     //DEFAULT = No Size Limit (An integer value that specifies the size limit for the search. For Active Directory, the size limit specifies the maximum number of returned objects.)
//$objCommand->Properties['Sort On']->Value = "name";     //DEFAULT = No Sorting (aka none)
$objCommand->Properties["Time Limit"] = 0;     //DEFAULT = No Time Limit (An integer value that specifies the time limit, in seconds, for the search.)
$objCommand->Properties["Timeout"] = 0;     //DEFAULT =  No Time Out (An integer value that specifies the client-side timeout value, in seconds.)

$Cmd  = "<LDAP://192.168.1.1>;(objectClass=*);adspath;Subtree";     //YOUR AD SEVER HERE | FQDN or IP
$objCommand->CommandText = $Cmd ;
$objRecordSet = $objCommand->Execute() ;
$OrderNumber = 0;
while(!$objRecordSet->EOF())
 {
  $OrderNumber ++ ;
  $adspath = $objRecordSet->Fields['adspath']->Value ;
  echo $adspath ."<br>" ;
  $objRecordSet->MoveNext() ;
 }

$objRecordSet->Close() ;
//$objCommand->Close() ;
$objConnection->Close() ;
unset($objRecordSet) ;
unset($objCommand) ;
unset($objConnection) ;

?>
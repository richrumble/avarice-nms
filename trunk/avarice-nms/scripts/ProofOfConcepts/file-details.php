<?PHP
//http://technet.microsoft.com/en-us/library/ee176615.aspx
header("Content-Type: text/plain");
$arrHeaders = array(284);  //XP Only has 40 or so possible meta-data types
$objShell = new COM("Shell.Application");
$objFolder = $objShell->Namespace('c:\temp');  //Change to suit the proper path
for ($i=0; $i<=283; $i=$i+1) {
  $arrHeaders[$i]=$objFolder->GetDetailsOf($objFolder->Items, $i);
};
foreach ($objFolder->Items as $strFileName) {
  for ($i=0; $i<=283; $i=$i+1) {
		Echo $i . "\t" . $arrHeaders[$i] . ": " . $objFolder->GetDetailsOf($strFileName, $i) . "\r\n";
  }
};
?>
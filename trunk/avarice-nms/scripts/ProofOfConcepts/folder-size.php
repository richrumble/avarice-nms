<?PHP
header("Content-Type: text/plain");
$objFSO = new COM("Scripting.FileSystemObject");
$objFolder = $objFSO->GetFolder("C:\\\\Documents and Settings\\");
Echo "Date created: " . $objFolder->DateCreated . "\r\n";
Echo "Date last accessed: " . $objFolder->DateLastAccessed . "\r\n";
Echo "Date last modified: " . $objFolder->DateLastModified . "\r\n";
Echo "Drive: " . $objFolder->Drive . "\r\n";
Echo "Is root folder: " . $objFolder->IsRootFolder . "\r\n";
Echo "Name: " . $objFolder->Name . "\r\n";
Echo "Parent folder: " . $objFolder->ParentFolder . "\r\n";
Echo "Path: " . $objFolder->Path . "\r\n";
Echo "Short name: " . $objFolder->ShortName . "\r\n";
Echo "Short path: " . $objFolder->ShortPath . "\r\n";
Echo "Size: " . $objFolder->Size . "\r\n";
Echo "Type: " . $objFolder->Type . "\r\n";
Echo "=======================";

?>
<?PHP
$objFSO = new COM("Scripting.FileSystemObject");
$objFolder = $objFSO->GetFolder("C:\\\\windows\\");
Echo "Date created: " . $objFolder->DateCreated . "<br />";
Echo "Date last accessed: " . $objFolder->DateLastAccessed . "<br />";
Echo "Date last modified: " . $objFolder->DateLastModified . "<br />";
Echo "Drive: " . $objFolder->Drive . "<br />";
Echo "Is root folder: " . $objFolder->IsRootFolder . "<br />";
Echo "Name: " . $objFolder->Name . "<br />";
Echo "Parent folder: " . $objFolder->ParentFolder . "<br />";
Echo "Path: " . $objFolder->Path . "<br />";
Echo "Short name: " . $objFolder->ShortName . "<br />";
Echo "Short path: " . $objFolder->ShortPath . "<br />";
Echo "Size: " . $objFolder->Size . "<br />";
Echo "Type: " . $objFolder->Type . "<br />";


?>
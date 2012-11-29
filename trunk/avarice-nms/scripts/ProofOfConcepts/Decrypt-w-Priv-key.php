<?PHP
// Load the private key from the private.key file
$privateKey = openssl_get_privatekey(file_get_contents('srvr-priv.key'));

// Setup a couple filenames to load the text and its key from
$dataFile = 'data.txt';
$keyFile = 'data.key';

// Read the previously encrypted file and the key used to encrypt it
$encryptedData = file_get_contents($dataFile);
$encryptedKey = file_get_contents($keyFile);

// Decrypt the data with our $privateKey and store the result in $decryptedData
$result = openssl_open(base64_decode($encryptedData), $decryptedData, base64_decode($encryptedKey), $privateKey);

// Show if it was a success or failure
if ($result) {
  echo "Success.\n";
} else {
  echo "Failure.\n";
}

// Store it locally
$localFile = 'decrypted.txt';
file_put_contents($localFile, $decryptedData);
?>
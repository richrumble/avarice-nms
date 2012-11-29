<?PHP 
// Get the full message text into a variable
$fullText = 'My Hovercraft is full of eels';
$encryptedText = '';
// Load the public key into an array
$publicKeys[] = openssl_get_publickey(file_get_contents('srvr-pub.key'));
// Encrypt the $fullText and return the $encryptedText and the $encryptedKeys
$res = openssl_seal($fullText, $encryptedText, $encryptedKeys, $publicKeys);
// Setup a couple filenames to store the text and its key
$dataFile = 'data.txt';
$keyFile = 'data.key';
// Write the order files
file_put_contents($dataFile, base64_encode($encryptedText));
file_put_contents($keyFile, base64_encode($encryptedKeys[0]));

/*

//  ---------Another Method------------
//  ------ENCRYPT------
$plaintext = 'My Hovercraft is full of eels';
$publicKey = openssl_pkey_get_public('file://public.key');
$encrypted = '';
$a_envelope = array();
$a_key = array($publicKey);
if (openssl_seal($plaintext, $encrypted, $a_envelope, $a_key) === FALSE)
    die('Failed to encrypt data');
openssl_free_key($publicKey);

//  ------DECRYPT------
if (!$privateKey = openssl_pkey_get_private('file://private.key'))
    die('Private Key failed');
$decrypted = '';
if (openssl_open($encrypted, $decrypted, $envelope, $privateKey) === FALSE)
    die('Failed to decrypt data');
openssl_free_key($privateKey);

*/

?>
<?php
header("Content-Type: text/plain");
// Create the keypair
$config = array("digest_alg" => "sha1", "private_key_bits" => 2048, "private_key_type" => OPENSSL_KEYTYPE_RSA);
$res=openssl_pkey_new($config);

// Get private key
openssl_pkey_export($res, $privkey);

// Get public key
$pubkey=openssl_pkey_get_details($res);
//var_dump(openssl_pkey_get_details($res));

/*
This routine dumps the PUBLIC key from the PRIVATE key
$a_key = openssl_pkey_get_details($privateKey);
print_r($a_key);
*/

$pubkey=$pubkey["key"];
echo "\r\n";
print $pubkey;
echo "\r\n";
print $privkey;

// $ciphers             = openssl_get_cipher_methods();
// $ciphers_and_aliases = openssl_get_cipher_methods(true);
// $cipher_aliases      = array_diff($ciphers_and_aliases, $ciphers);
// 
// print_r($ciphers);
// echo "\r\n";
// print_r($cipher_aliases);
// echo "\r\n";

/*   Output should look like so, PEM Format(2048 bits)
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmehu5vyCKOJXEdkb1aam
XQGuS9it+TGSV+XhnmDX56lNWY8HE2/R1FYlD0B+3ig4Yc6RvPYVChYgxOsr4tqU
rIie0q4DTT21JZyh/UjxEzcNmolxiM89izJViIdIXmQNePj6JVMQP0S7wzzA0IHe
gcZOc+DORM2yqZc8tOG8BFzISGbELSZxrsJVnkaro3jDIfNWVdOcmgGyQ7vq+sK5
4OKLpwRXbxc5bCU7e4PNioIvipomZgmNTLb/op0UqyJXC8UU4AzocyCvqpixqrNm
spsS+pxVoTaLj7kx0Lp8toj+nYt4dT+GHu20N4LU0XYW5NxHY60sAHV4S6zqL1Ln
vwIDAQAB
-----END PUBLIC KEY-----

-----BEGIN RSA PRIVATE KEY-----
MIIEowIBAAKCAQEAmehu5vyCKOJXEdkb1aamXQGuS9it+TGSV+XhnmDX56lNWY8H
E2/R1FYlD0B+3ig4Yc6RvPYVChYgxOsr4tqUrIie0q4DTT21JZyh/UjxEzcNmolx
iM89izJViIdIXmQNePj6JVMQP0S7wzzA0IHegcZOc+DORM2yqZc8tOG8BFzISGbE
LSZxrsJVnkaro3jDIfNWVdOcmgGyQ7vq+sK54OKLpwRXbxc5bCU7e4PNioIvipom
ZgmNTLb/op0UqyJXC8UU4AzocyCvqpixqrNmspsS+pxVoTaLj7kx0Lp8toj+nYt4
dT+GHu20N4LU0XYW5NxHY60sAHV4S6zqL1LnvwIDAQABAoIBAFc0ulIrVVzxEsDM
MddXPACLCUV0mu8NxYwEd1iUKwHajjdmsW2z+ELGJO2Fb91VEM1qjG1zSeyTKTIq
xo0dqQwxxTw+2SQVCOUs4SRxBhgLI6wioLASLR8IACsB7GF/c5rOCH7Jl/Uhbvpr
HB2I2JjT0nQaNwYJP2iUhxM+rJgTUW68T0KUUYtjbgPbyVtRqbsQSP82k1iNRY/Y
c2Sp9nAPFHSQx0NvsB3Nd0etYkHODtKoWAhhoURepGWqP57JzEd5GnrznkcEbsuO
ybvdTClgOgPRUU9YpgcseUxKmuC9m/VG9VQnAxxQ2etc8vnClSFldMOBGdpq3Gw0
gU/mZOECgYEAy07YnY4G02tQr+wkD2WBeJHi3By7aW1CMsbIX/S0GEVSEegZbJMK
FUhGthfFS/okqSjLszUPwYAh8kfrqn+Q3faXehZpreKxc1xI5QMrGPdFNqkyD5Cq
2VyGfhwAIJRhLrmx6vmufFVPa7L8XAUwumjURHE1griqf3oyNCx6UZcCgYEAwcv5
aDDSvGtsETgEYPzhStVgRIJBj0w8QArQoyiZrLh3lblPrjuRPwQo8NEsltSZeGx7
6StdzVtwd7LAfWWsq02k1DbBIFJJnBOlpdZELWwNYb5GdW9vL+DBuHwvN9WaCj+E
7p/lNGWWWoXwf5447qZz34h+mdrnpR5MlHnkkBkCgYAYY68Mx5r+BeO6FwQEbNLp
WebzLOc2sIq+eKZVDJAGUVqVF1jlc4ZEI1WIRrW+dZWsTV6Scw0e03Y/EG2vGHqS
hiCJ3uJyn71V7343KvgimJdPBWEiCOLWDIl923IQdnxqFJDJdYZ/F+TdMK2hhGXi
Fu1QAJzAv9KEVG5tX+CUpQKBgE/zM3B9e+MJuiqcXooYqWUzBCUfS6avf8e37nzx
OfzcmVEmgy3RG0nREIbQ9MFA8hORRcla/7bBu3NWRj01XffJ728xK5mG+SEvpc31
yGioxBiH5A98qnUpFyJh8STqtUL3E4NSab+lDVNJIH+1qa0i9HjKmdglTB19k4BJ
jTa5AoGBAJ0yQrzScnqGFYOK7fz8q7d5TfTSbDj/ba+xmGe/CHFUYpE4gIoHLsJ7
Hr/L7ulxQLcMhgoW278m5Ed3cWiXJk7rlz0Du68v+CNLhPadLwnjvwpFj1z+ZZ+w
EIzbC93ZGAUKtBH5i1euh+i7A3REf/VddTtfonKWnv3E/CWtyyMi
-----END RSA PRIVATE KEY-----
*/
?>
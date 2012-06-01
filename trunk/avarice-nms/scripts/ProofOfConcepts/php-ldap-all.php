<?php
ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
$connect = ldap_connect("10.5.3.3");
$filter = "(objectclass=*)";
$base_dn = "DC=global,DC=ad";
$ldap_user ="CN=Eaglesfield\, Andy,OU=NOCUsers,DC=ops,DC=global,DC=ad";
$ldap_pass = "SIX&*ninet1n11))";
//$bind = ldap_bind($connect) // asume anon connect or add user/pass
$bind = ldap_bind($connect, $ldap_user, $ldap_pass)
     or exit(">>Could not bind to $ldap_host<<");
$read = ldap_search($connect, $base_dn, $filter)
     or exit(">>Unable to search ldap server<<");
$info = ldap_get_entries($connect, $read);
echo $info["count"]." entries returned<br>";
for ($i = 0; $i<$info["count"]; $i++) {
  for ($ii=0; $ii<$info[$i]["count"]; $ii++){
     $data = $info[$i][$ii];
     for ($iii=0; $iii<$info[$i][$data]["count"]; $iii++) {
       echo $data.",".$info[$i][$data][$iii]."\n";
     }
  }
}
?>
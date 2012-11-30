<?php
ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
$connect = ldap_connect("192.168.1.1");
$filter = "(objectclass=*)";
$base_dn = "DC=example,DC=com";
$ldap_user ="CN=Doe\, John,OU=Users,DC=users,DC=example,DC=com";
$ldap_pass = "))";
//$bind = ldap_bind($connect) // assume anon connect or add user/pass
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
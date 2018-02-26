<?php

$ldap_server_url = "ldaps://ldap1.3turn.symas.net:38900";	//LDAP SERVER URL
$ldaprdn  = "cn=Manager,o=threeTurn";     // SERVICE ACCOUNT DN
$ldappass = "JuRoxScibTurn3";  // SERVICE ACCOUNT PASSWORD

$ldap_search_base = "ou=players,o=threeTurn";  //Search Base
$ldap_search_filter = "(&(objectClass=*)(ejAcctName=gauravsood91))"; //Search filter

$ldapconn = @ldap_connect($ldap_server_url);

ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

if ($ldapconn) {

	$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
    if ($ldapbind) {
        echo "LDAP bind successful...<br>";
		$user_search_result = ldap_search($ldapconn, $ldap_search_base, $ldap_search_filter);
		$info = ldap_first_entry($ldapconn, $user_search_result);
		$dn = ldap_get_dn($ldapconn, $info);
		echo "Distinguished Name: <br>" . $dn;
    } else {
		echo ldap_errno($ldapconn) . ": " . ldap_error($ldapconn);
    }

}

?>

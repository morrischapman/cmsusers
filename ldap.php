<?php

    $ldap_server_host = '194.78.115.230';
//    $ldap_server_host = '194.78.115.226';
//    $ldap_server_host = '127.0.0.1';
    $ldap_server_port = '8443';

    $ldap_base_dn = ',dc=efpia,dc=eu';
//    $ldap_user_dn = 'ou=people,dc=efpia,dc=eu';
//    $ldap_group_dn = 'ou=groups,dc=efpia,dc=eu';

    $ldap_user = 'ebe';
    $ldap_password = 'QspHA3h3db8N';

    $ldapconn = ldap_connect($ldap_server_host, $ldap_server_port)   or die("Could not connect to $ldap_server_host");

    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

    if ($ldapconn) {

        $ldaprdn = "cn=" . $ldap_user . $ldap_base_dn;
//        $ldaprdn = "uid=$ldap_user,$ldap_user_dn";
//        $ldaprdn = "uid=$ldap_user,$ldap_group_dn";


        // binding to ldap server
        $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldap_password);

        // verify binding
        if ($ldapbind) {
            echo "LDAP bind successful...";
        } else {
            echo "LDAP bind failed...\n";
            var_dump(ldap_error($ldapconn));
        }

    }
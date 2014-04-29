

#--<HTTPAUTH>--
AuthType Basic
AuthName "{$auth_name}"
{if isset($ldap_uri)}

AuthBasicProvider ldap
AuthzLDAPAuthoritative off
AuthLDAPURL "{$ldap_uri}"

AuthLDAPBindDN "{$ldap_bind_dn}"
AuthLDAPBindPassword    "{$ldap_bind_password}"

{else}

AuthUserFile {$htpasswd_file}
AuthGroupFile /dev/null

{/if}

Require valid-user
#--</HTTPAUTH>--
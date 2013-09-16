<?php
if(!cmsms()) exit;
$db = $this->GetDb();
$dict = NewDataDictionary($db);

switch(true) {
	case version_compare($oldversion, '0.0.7', '<'):
		$sql = $dict->AddColumnSQL(cms_db_prefix() . 'module_cmsusers_users', 'is_disabled I');
		$dict->ExecuteSQLArray($sql);
	case version_compare($oldversion, '1.0.3', '<'):
		$sql = $dict->AddColumnSQL(cms_db_prefix() . 'module_cmsusers_users', 'htpassword C(255)');
		$dict->ExecuteSQLArray($sql);
    case version_compare($oldversion, '1.0.15', '<'):
        $sql = $dict->AddColumnSQL(cms_db_prefix() . 'module_cmsusers_users', 'is_ldap I');
        $dict->ExecuteSQLArray($sql);
        $this->SetPreference('ldap_server_port',  389);
}
<?php

if(!cmsms()) exit;
$db = $this->GetDb();
$dict = NewDataDictionary($db);

$flds = array(
	'id I KEY AUTO',
	'username C(255)',
	'algorithm C(255)',
	'salt C(255)',
	'password C(255)',
	'htpassword C(255)',
	'email C(255)',
	'is_active I',
	'is_disabled I',
	'token C(255)',
	'created_at DT',
	'updated_at DT',
	'last_login DT',
    'is_ldap I',
	'comments	XL'
);

$sql = $dict->CreateTableSQL(cms_db_prefix() . 'module_cmsusers_users', implode(',', $flds), array('mysql' => 'TYPE=MyISAM'));
$dict->ExecuteSQLArray($sql);
$sql = $dict->CreateIndexSQL('user_index', cms_db_prefix() . 'module_cmsusers_users', 'id, username, email, is_active');
$dict->ExecuteSQLArray($sql);


$flds = array(
	'id I KEY AUTO',
	'user_id I',
	'created_at DT',
	'updated_at DT',
	);
	
$sql = $dict->CreateTableSQL(cms_db_prefix() . 'module_cmsusers_profiles', implode(',', $flds), array('mysql' => 'TYPE=MyISAM'));
$dict->ExecuteSQLArray($sql);
$sql = $dict->CreateIndexSQL('profile_index', cms_db_prefix() . 'module_cmsusers_profiles', 'id, user_id');
$dict->ExecuteSQLArray($sql);


$flds = array(
	'id I KEY AUTO',
	'name C(255)',
	'type C(255)',
	'is_active I',
	'created_at DT',
	'updated_at DT',
	'comments	XL'
	);
	
$sql = $dict->CreateTableSQL(cms_db_prefix() . 'module_cmsusers_groups', implode(',', $flds), array('mysql' => 'TYPE=MyISAM'));
$dict->ExecuteSQLArray($sql);
$sql = $dict->CreateIndexSQL('groups_index', cms_db_prefix() . 'module_cmsusers_groups', 'id, is_active, type');
$dict->ExecuteSQLArray($sql);

$flds = array(
	'id I KEY AUTO',
	'group_id I',
	'user_id I',
	'created_at DT',
	'updated_at DT'
	);
	
$sql = $dict->CreateTableSQL(cms_db_prefix() . 'module_cmsusers_usergroups', implode(',', $flds), array('mysql' => 'TYPE=MyISAM'));
$dict->ExecuteSQLArray($sql);
$sql = $dict->CreateIndexSQL('usergroups_index', cms_db_prefix() . 'module_cmsusers_usergroups', 'id, group_id, user_id');
$dict->ExecuteSQLArray($sql);

$flds = array(
	'id I KEY AUTO',
	'name C(255)',
	'is_active I',
	'created_at DT',
	'updated_at DT',
	'comments XL'
	);
	
$sql = $dict->CreateTableSQL(cms_db_prefix() . 'module_cmsusers_permissions', implode(',', $flds), array('mysql' => 'TYPE=MyISAM'));
$dict->ExecuteSQLArray($sql);
$sql = $dict->CreateIndexSQL('permissions_index', cms_db_prefix() . 'module_cmsusers_permissions', 'id, name, is_active');
$dict->ExecuteSQLArray($sql);


$flds = array(
	'id I KEY AUTO',
	'group_id I',
	'permission_id I',
	'created_at DT',
	'updated_at DT'
	);
	
$sql = $dict->CreateTableSQL(cms_db_prefix() . 'module_cmsusers_grouppermissions', implode(',', $flds), array('mysql' => 'TYPE=MyISAM'));
$dict->ExecuteSQLArray($sql);
$sql = $dict->CreateIndexSQL('grouppermissions_index', cms_db_prefix() . 'module_cmsusers_grouppermissions', 'id, group_id,permission_id');
$dict->ExecuteSQLArray($sql);

$flds = array(
	'id I KEY AUTO',
	'group_id I',
	'profile_field_id I',
	'is_mandatory I',
	'created_at DT',
	'updated_at DT',
	'comments XL' 
	);
	
$sql = $dict->CreateTableSQL(cms_db_prefix() . 'module_cmsusers_group_profile_fields', implode(',', $flds), array('mysql' => 'TYPE=MyISAM'));
$dict->ExecuteSQLArray($sql);
$sql = $dict->CreateIndexSQL('group_profile_fields_index', cms_db_prefix() . 'module_cmsusers_group_profile_fields', 'id, group_id,profile_field_id');
$dict->ExecuteSQLArray($sql);

$flds = array(
	'id I KEY AUTO',
	'name C(255)',
	'fname C(255)',
	'type	C(255)',
	'tips C(255)', 
	'options XL',
	'is_active I',
	'created_at DT',
	'updated_at DT',
	'comments XL'
	);
	
$sql = $dict->CreateTableSQL(cms_db_prefix() . 'module_cmsusers_profile_fields', implode(',', $flds), array('mysql' => 'TYPE=MyISAM'));
$dict->ExecuteSQLArray($sql);
$sql = $dict->CreateIndexSQL('profile_fields_index', cms_db_prefix() . 'module_cmsusers_profile_fields', 'id, name, fname');
$dict->ExecuteSQLArray($sql);


$flds = array(
  'id I KEY AUTO',
  'issued_date I',
  'user_id  I',
  'cookie_value XL',
  'remote_information XL'
);

$sql = $dict->CreateTableSQL(cms_db_prefix() . 'module_cmsusers_sessions', implode(',', $flds), array('mysql' => 'TYPE=MyISAM'));
$dict->ExecuteSQLArray($sql);
$sql = $dict->CreateIndexSQL('profile_fields_index', cms_db_prefix() . 'module_cmsusers_sessions', 'id, user_id, cookie_value');
$dict->ExecuteSQLArray($sql);


// Permissions

$this->CreatePermission('Manage CMSUsers', 'Manage CMSUsers');	


// PREFERENCES
$config = cms_utils::get_config();
$this->SetPreference('signup_email_subject', $this->lang('account details'));
$this->SetPreference('validation_email_subject',  $this->lang('account validation'));
$this->SetPreference('password_reset_email_subject',  $this->lang('account reset password'));
$this->SetPreference('htpassword_title',  $this->lang('authentication_title'));
$this->SetPreference('htpassword_path',  $config['root_path']);
$this->SetPreference('ldap_server_port',  389);
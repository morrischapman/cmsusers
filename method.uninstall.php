<?php
if(!cmsms()) exit;
if (! $this->CheckAccess()) // Restrict to admin panel and groups with permission
{
	return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	exit;
}


$databases = array(
	'module_cmsusers_users',
	'module_cmsusers_profiles',
	'module_cmsusers_groups',
	'module_cmsusers_usergroups',
	'module_cmsusers_permissions',
	'module_cmsusers_grouppermissions',
	'module_cmsusers_group_profile_fields',
	'module_cmsusers_profile_fields',
	'module_cmsusers_sessions',
	);
	
		
// Typical Database Removal
$db =& cms_utils::get_db();
	
// remove the database table
$dict = NewDataDictionary( $db );
		
foreach($databases as $database)
{
	$sqlarray = $dict->DropTableSQL( cms_db_prefix() . $database );
	$dict->ExecuteSQLArray($sqlarray);	
}

$this->RemovePermission();
$this->RemovePreference();


$this->Audit( 0, $this->GetFriendlyName(), $this->Lang('uninstalled'));
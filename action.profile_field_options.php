<?php
if(!cmsms()) exit;
if (! $this->CheckAccess()) // Restrict to admin panel and users with permission
{
	return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	exit;
} 

if(isset($params['profile_field_id']))
{
	$profile_field = CMSProfileField::retrieveByPk($params['profile_field_id']);
	
	if ($profile_field)
	{
		switch($params['maction'])
		{
			case 'change_status':
				$profile_field->is_active = ($profile_field->is_active == 0)?1:0;
				$profile_field->save();
				break;
			case 'delete':
				$profile_field->delete();
				break;
		}
	}
}

return $this->Redirect($id,'defaultadmin',$returnid, array('tab' => 'profile_fields'));
<?php
if(!cmsms()) exit;
if (! $this->CheckAccess()) // Restrict to admin panel and users with permission
{
	return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	exit;
} 

if(isset($params['permission_id']))
{
	$permission = CMSPermission::retrieveByPk($params['permission_id']);
	
	if ($permission)
	{
		switch($params['maction'])
		{
			case 'change_status':
				$permission->is_active = ($permission->is_active == 0)?1:0;
				$permission->save();
				break;
			case 'delete':
				$permission->delete();
				break;
		}
	}
}

return $this->Redirect($id,'defaultadmin',$returnid, array('tab' => 'permissions'));
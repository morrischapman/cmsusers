<?php
if(!cmsms()) exit;
if (! $this->CheckAccess()) // Restrict to admin panel and users with permission
{
	return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	exit;
} 

if(isset($params['user_id']))
{
	$user = CMSUser::retrieveByPk($params['user_id']);
	
	if ($user)
	{
		switch($params['maction'])
		{
			case 'change_status':
				$user->is_active = ($user->is_active == 0)?1:0;
				$user->save();
				break;
			case 'delete':
				$user->delete();
				break;
		}
	}
}

return $this->Redirect($id,'defaultadmin',$returnid, array('tab' => 'users'));
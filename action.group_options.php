<?php
if(!cmsms()) exit;
if (! $this->CheckAccess()) // Restrict to admin panel and users with permission
{
	return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	exit;
} 

if(isset($params['group_id']))
{
	$group = CMSGroup::retrieveByPk($params['group_id']);
	
	if ($group)
	{
		switch($params['maction'])
		{
			case 'change_status':
				$group->is_active = ($group->is_active == 0)?1:0;
				$group->save();
				break;
			case 'delete':
				$group->delete();
				break;
		}
	}
}

return $this->Redirect($id,'defaultadmin',$returnid, array('tab' => 'groups'));
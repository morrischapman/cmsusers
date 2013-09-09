<?php
if(!cmsms()) exit;

if(isset($params['user_id']) && isset($params['token']))
{
	$user = CMSUser::doSelectOne(
		array(
			'where' => array(
				'id' => $params['user_id'],
				'token' => $params['token']
				)
			));
			
		if (!empty($user))
		{
			$user->is_active = 1;
			$user->save();
			$this->smarty->assign('message', $this->lang('user validated'));
			echo $this->ProcessTemplateFor('validate_success');
			return;
		}
		
}		

$this->smarty->assign('message', $this->lang('invalid token or user unknown'));
echo $this->ProcessTemplateFor('validate_error');
return;

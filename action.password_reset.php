<?php
if(!cmsms()) exit;

if (isset($params['user_id']) && isset($params['token']))
{
	$user = CMSUser::doSelectOne(array(
		'where' => array(
			'id' => $params['user_id'],
			'token' => $params['token']
			)
		));
			
	if(is_object($user))
	{
		$form = new CMSForm($this->GetName(), $id, 'password_reset',$returnid);
		$form->setButtons(array('submit'));
		$form->setLabel('submit',$this->lang('change password'));
		$form->setWidget('user_id','hidden');
		$form->setWidget('token','hidden');
		$form->setWidget('new_password', 'password', array('validators' => array('not_empty' => true)));
		$form->setWidget('confirm_password', 'password',array('validators' => array('equal_field' => 'new_password')));
		
		
		if ($form->isPosted())
		{
			$form->process();
			
			if (!$form->hasErrors())
			{
					$user->setPassword($form->getWidget('new_password')->getValue());
					$user->generateToken(); // We regenerate a new token to avoid reuse of it.
					$user->save();
					
					$this->smarty->assign('success_message', $this->lang('password changed successfully'));
					echo $this->ProcessTemplateFor('password_change_success', $params);
					return;
			}
		}
		
		$this->smarty->assign('title', $this->lang('password reset for', $user->username));
		$this->smarty->assign('form',$form);
		
		echo $this->ProcessTemplateFor('password_reset_form', $params);	
		return;
	}	
}

$this->smarty->assign('message', $this->lang('invalid token or user unknown'));
echo $this->ProcessTemplateFor('password_reset_error');
return;

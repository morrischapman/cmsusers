<?php
if(!cmsms()) exit;

if ($user = CMSUsers::getUser())
{
	$form = new CMSForm($this->GetName(), $id, 'password_change',$returnid);
	$form->setButtons(array('submit'));
	$form->setLabel('submit', $this->lang('change password'));

	$form->setWidget('redirect', 'hidden');
	$form->setWidget('current_password', 'password', array('validators' => array('not_empty' => true)));
	$form->setWidget('new_password', 'password', array('validators' => array('not_empty' => true)));
	$form->setWidget('confirm_password', 'password',array('validators' => array('equal_field' => 'new_password')));

	if($form->isPosted())
	{
		$form->process();
		if($user->checkPassword($form->getWidget('current_password')->getValue()) === false)
		{
			$form->getWidget('current_password')->setError($this->lang('wrong password'),'form error');
		}
		
		if (!$form->hasErrors())
		{
			$user->setPassword($form->getWidget('new_password')->getValue());
			$user->save();
			
			if (!$form->getWidget('redirect')->isEmpty())
			{
				return $this->Redirect($id,$form->getWidget('redirect')->getValue(),$returnid);
			}
			
			$this->smarty->assign('success_message', $this->lang('password changed successfully'));
			echo $this->ProcessTemplateFor('password_change_success', $params);
			return;
		}
	}

  $this->smarty->assign('change_password', $this->lang('change password'));
	$this->smarty->assign('form', $form);
	echo $this->ProcessTemplateFor('password_change_form', $params);
	return;
}
else
{
	return $this->Redirect($id,'signin',$returnid, array('redirect' => 'password_change'));
}
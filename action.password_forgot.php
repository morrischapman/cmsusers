<?php
if(!cmsms()) exit;

$this->smarty->assign('title', $this->lang('password_forgot'));

$form = new CMSForm($this->GetName(), $id, 'password_forgot', $returnid);
$form->setButtons(array('submit'));
$form->setWidget('email', 'text', array('validators' => array('email' => true,'not_empty' => true), 'tips' => $this->lang('enter registered email')));

if($form->isPosted())
{
	if (!$form->hasErrors())
	{
		$users = CMSUser::retrieveByEmail($form->getWidget('email')->getValue());
		if(!empty($users))
		{
			foreach($users as $user)
			{
					$this->sendEmail('password_reset', &$user,$id,$returnid);
			}
		}
	}
}


$this->smarty->assign('form', $form);
echo $this->ProcessTemplateFor('password_forgot_form', $params);	
<?php
if(!cmsms()) exit;

if ($this->getPreference('allow_signup') != 1)
{
	$this->smarty->assign('message', $this->lang('signup disabled'));
	echo $this->ProcessTemplateFor('signup_disabled');
	return;
}


$form = new CMSForm($this->GetName(), $id, 'signup',$returnid);
$form->setButtons(array('submit'));
$form->setLabel('submit', $this->lang('create my account'));
$user = new CMSUser();
$profile = new CMSProfile();

$form->setWidget('user_id', 'hidden', array('object' => &$user, 'field_name' => 'id', 'get_method' => 'getId'));
$form->setWidget('redirect', 'hidden');
$form->setWidget('redirect_url', 'hidden');
$form->setWidget('email', 'text', array('object' => &$user, 'validators' => array('not_empty' => true, 'email' => true)));

if ($this->GetPreference('signup_use_email_as_login') == 1)
{
  $form->getWidget('email')->addValidator('unique','CMSUser::retrieveByUsername');
}

if ($this->GetPreference('signup_email_unique') == 1)
{
			$form->getWidget('email')->addValidator('unique','CMSUser::retrieveOneByEmail');
}

if($this->GetPreference('signup_use_email_as_login') != 1)
{
  $form->setWidget('username', 'text', array('object' => &$user, 'validators' => array('not_empty' => true, 'unique' =>'CMSUser::retrieveByUsername')));
}

if($this->GetPreference('signup_generate_password') != 1)
{
 $form->setWidget('password', 'password', array('validators' => array('equal_field' => 'confirm_password')));
 $form->setWidget('confirm_password', 'password'); 
}


$group = CMSGroup::retrieveByPk(isset($params['group'])?$params['group']:$this->getPreference('default_group_for_signup'));

if (is_object($group))
{
	$fields = $group->getProfileFields();
	
	foreach ($fields as $field)
	{
		$options = array_merge(
			array('label' => $field->name, 'tips' => $field->tips, 'object' => $profile),
			$field->getOptionsToArray()
			);

		//TODO: Treath the field option
		$form->setWidget($field->fname, $field->type, $options);
	} 
}

if($this->GetPreference('profile_module') != '')
{  
  $class_name = $this->GetPreference('profile_module');
  $module = cms_utils::get_module($class_name);
  $object_name = $class_name.'Object';
  $views_name = $class_name.'Views';
  
  $profile_module = new $object_name; 
  
  $form->setWidget('title', 'text', array('object' => &$profile_module, 'size' => 50, 'label' => $module->getTitleLabel()));
  call_user_func(array($views_name, 'createForm'), $module, &$form, $profile_module,array('frontend' => true));
}

if (($this->GetPreference('signup_use_captcha') == 1) && cmsms()->GetModuleOperations()->IsModuleActive('Captcha'))
{
	if(cms_utils::get_module('Captcha')->getPreference('active_lib') == 'recaptcha')
	{
		$form->setWidget('captcha', 'static', array('value' => cms_utils::get_module('Captcha')->getCaptcha()));
	}
	else
	{
		$form->setWidget('captcha', 'text', array(
			'size' => 10, 
			'label' => cms_utils::get_module('Captcha')->getCaptcha(),
			'tips' => $this->lang('captcha_tips')
			));
	}
	
	

}

if ($form->isPosted())
{
	$form->process();
	
	if (($this->GetPreference('signup_use_captcha') == 1) && cmsms()->GetModuleOperations()->IsModuleActive('Captcha'))
	{
		if(!cms_utils::get_module('Captcha')->checkCaptcha($form->getWidget('captcha')->getValue()))
		{
			$form->getWidget('captcha')->setError($this->lang('invalid captcha'), 'form error');
		}
	}
	
	if (!$form->hasErrors())
	{
	  if($this->GetPreference('signup_generate_password') != 1)
    {
		  if (!$form->getWidget('password')->isEmpty())
		  {
			  $user->setPassword($form->getWidget('password')->getValue());
		  }
		}
		else
		{
		  $this->smarty->assign('password', $user->generatePassword());
		}
		
		if ($this->GetPreference('signup_use_email_as_login') == 1)
    {
      $user->username = $form->getWidget('email')->getValue();
    }
    
		$user->save();
		if ($user->getId() != '')
		{
			if (is_object($group))
			{
				$group->addUser($user->getId());
			}
			$profile->user_id = $user->getId();
			$profile->save();	
			
			if($this->GetPreference('profile_module') != '')
      {  
        $profile_module->setUserId($user->getId());
        $profile_module->save(array('frontend' => true));
      }
    	
		}
		
		$validation = $this->getPreference('signup_validation', 'none');
		switch($validation)
		{
			case 'admin':
				$this->smarty->assign('success_message', $this->lang('account created review by admin'));
				break;			
			case 'email':
				$this->sendEmail('validation', $user,$id,$returnid);
				$this->smarty->assign('success_message', $this->lang('account created email sent', $user->email));
				break;
			default: // SAME AS CASE 'none';
				$user->is_active = 1;
				$this->smarty->assign('success_message', $this->lang('account created'));
				break;
		}
		
		if ($this->GetPreference('send_signup_email') == 1)
		{
				$this->sendEmail('signup', $user,$id,$returnid);
		}
		
		$user->save();
		
		if(($this->GetPreference('signup_validation', 'none') == 'none') && ($this->GetPreference('signup_automatically_login') == 1))
		{
		  $_SESSION['modules']['CMSUsers']['user_id'] = $user->getId();
		}
		
		if (!$form->getWidget('redirect_url')->isEmpty())
		{
				return CMSUsers::jumpTo($form->getWidget('redirect_url')->getValue());
		}
		elseif($this->GetPreference('signup_redirection') != '')
		{
			return CMSUsers::jumpTo($this->GetPreference('signup_redirection'));
		}
				
		echo $this->ProcessTemplateFor('signup_success');
		return;
	}
}

$this->smarty->assign('title', $this->lang('signup'));
$this->smarty->assign('form', $form);

echo $this->ProcessTemplateFor('signup_form', $params);
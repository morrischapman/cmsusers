<?php
if(!cmsms()) exit;

if ($user = CMSUsers::getUser())
{
	$form = new CMSForm($this->GetName(), $id, 'profile_edit',$returnid);
	$form->setButtons(array('submit'));
	$form->setLabel('submit', $this->lang('save'));
	$form->setWidget('redirect', 'hidden');
	$form->setWidget('email', 'text', array('object' => &$user, 'validators' => array('email' => true)));
	
	$profile = $user->getProfile();
	$fields = $profile->getUserProfileFields();
	
	foreach ($fields as $field)
	{
		$options = array_merge(
			array('label' => $field->name, 'tips' => $field->tips, 'object' => $profile),
			$field->getOptionsToArray()
			);

		//TODO: Treath the field option
		$form->setWidget($field->fname, $field->type, $options);
	}
	
	if($this->GetPreference('profile_module') != '')
  {  
    $class_name = $this->GetPreference('profile_module');
    $module = cms_utils::get_module($class_name);
    $object_name = $class_name.'Object';
    $views_name = $class_name.'Views';

    $c = new MCFCriteria();
    $c->add('user_id', $user->getId());
    $profile_module = $object_name::doSelectOne($c);

    $form->setWidget('title', 'text', array('object' => &$profile_module, 'size' => 50, 'label' => $module->getTitleLabel()));
    call_user_func(array($views_name, 'createForm'), $module, &$form, &$profile_module, array('frontend' => true));
  }
	
//	$form->setWidget('current_password', 'password', array('validators' => array('not_empty' => true)));
//	$form->setWidget('new_password', 'password', array('validators' => array('not_empty' => true)));
//	$form->setWidget('confirm_password', 'password',array('validators' => array('equal_field' => 'new_password')));

	if($form->isPosted())
	{
		$form->process();
				
		if (!$form->hasErrors())
		{
			//$user->setPassword($form->getWidget('new_password')->getValue());
			$user->save();
			
			if ($user->getId() != '')
			{
				$profile->user_id = $user->getId();
				$profile->save();	
				
				if($this->GetPreference('profile_module') != '')
        {  
          $profile_module->save();
        }
			}
			
			if (!$form->getWidget('redirect')->isEmpty())
			{
				return $this->Redirect($id,$form->getWidget('redirect')->getValue(),$returnid);
			}
			
			$this->smarty->assign('success_message', $this->lang('profile successfully edited'));
			echo $this->ProcessTemplateFor('profile_edit_success', $params);
			return;
		}
	}

  $this->smarty->assign('title', $this->lang('edit profile for', $user->username));
	$this->smarty->assign('form', $form);
	echo $this->ProcessTemplateFor('profile_edit_form', $params);
	return;
}
else
{
	return $this->Redirect($id,'signin',$returnid, array('redirect' => 'profile_edit'));
}
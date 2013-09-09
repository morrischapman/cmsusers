<?php
if(!cmsms()) exit;
if (! $this->CheckAccess()) // Restrict to admin panel and users with group
{
	return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	exit;
}

if (isset($params['profile_field_id']) && $params['profile_field_id'] != '')
{
	$profile_field = CMSProfileField::retrieveByPk($params['profile_field_id']);
}

if ((!isset($profile_field)) || is_null($profile_field))
{
	$profile_field = new CMSProfileField();
}

$form = new CMSForm($this->GetName(), $id, 'profile_field_manage',$returnid);
$form->setButtons(array('submit','apply','cancel'));

$form->setWidget('profile_field_id', 'hidden', array('object' => &$profile_field, 'field_name' => 'id', 'get_method' => 'getId'));
$form->setWidget('name', 'text', array('object' => &$profile_field, 'validators' => array('not_empty' => true)));
$form->setWidget('type', 'select', array('object' => &$profile_field, 
	'values' => CMSFormWidget::getFieldsList()
	));
	
	
$form->setWidget('tips', 'text', array('object' => &$profile_field));
$form->setWidget('options', 'text', array('object' => &$profile_field));
	
$form->setWidget('is_active', 'checkbox', array('object' => &$profile_field));

// $form->setFieldset($this->lang('users'));
// $form->getFieldset($this->lang('users'))->setWidget('users','select', array('values' => CMSUser::getUserList(), 'size' => 10, 'multiple' => true, 'expanded' => true));

$form->setFieldset($this->lang('groups'));
$form->getFieldset($this->lang('groups'))->setWidget('groups','select', array('values' => CMSGroup::getGroupList(), 'size' => 10, 'multiple' => true, 'expanded' => true));

if ($form->isCancelled())
{
	return $this->Redirect($id,'defaultadmin',$returnid,array('tab' => 'profile_fields'));
}

if ($form->isPosted())
{
	$form->process();
	
	if(!$form->hasErrors())
	{
		$profile_field->save();
		
		// ProfileFields to user relations
		//CMSUserProfileField::setProfileFieldUsers($profile_field->getId(), $form->getFieldset($this->lang('users'))->getWidget('users')->getValues());
		
		// ProfileFields to user relations
		CMSGroupProfileField::setProfileFieldGroups($profile_field->getId(), $form->getFieldset($this->lang('groups'))->getWidget('groups')->getValues());
		
		if($form->isSubmitted())
		{
			return $this->Redirect($id,'defaultadmin',$returnid,array('tab' => 'profile_fields'));
		}
	}
}

// Userprofile_fields values
if ($profile_field->getId() != null)
{
//	$form->getFieldset($this->lang('users'))->getWidget('users')->setValues(CMSUserProfileField::getUsersList($profile_field->getId(), true));
	$form->getFieldset($this->lang('groups'))->getWidget('groups')->setValues(CMSGroupProfileField::getGroupsList($profile_field->getId(), true));
}

$smarty->assign('form', $form);
$smarty->assign('title', $this->lang('profile_field'));
echo $this->ProcessTemplate('admin.profile_field_manage.tpl');
return;

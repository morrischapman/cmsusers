<?php
if(!cmsms()) exit;
if (! $this->CheckAccess()) // Restrict to admin panel and users with permission
{
	return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	exit;
}

if (isset($params['group_id']) && $params['group_id'] != '')
{
	$group = CMSGroup::retrieveByPk($params['group_id']);
}

if ((!isset($group)) || is_null($group))
{
	$group = new CMSGroup();
}

$form = new CMSForm($this->GetName(), $id, 'group_manage',$returnid);
$form->setButtons(array('submit','apply','cancel'));

$form->setWidget('group_id', 'hidden', array('object' => &$group, 'field_name' => 'id', 'get_method' => 'getId'));
$form->setWidget('name', 'text', array('object' => &$group, 'validators' => array('not_empty' => true)));
$form->setWidget('type', 'select', array('object' => &$group, 'values' => array(
	'private' => 'private',
	'public' => 'public',
	'closed' => 'closed',
	)));
$form->setWidget('is_active', 'checkbox', array('object' => &$group));

$form->setFieldset($this->lang('users'));
$form->getFieldset($this->lang('users'))->setWidget('users','select', array('values' => CMSUser::getUserList(), 'size' => 10, 'multiple' => true, 'expanded' => true));

$form->setFieldset($this->lang('permissions'));
$form->getFieldset($this->lang('permissions'))->setWidget('permissions','select', array('values' => CMSPermission::getPermissionList(), 'size' => 10, 'multiple' => true, 'expanded' => true));





if ($form->isCancelled())
{
	return $this->Redirect($id,'defaultadmin',$returnid,array('tab' => 'groups'));
}

if ($form->isPosted())
{
	$form->process();
	
	if(!$form->hasErrors())
	{
		$group->save();
		
		// Groups to user relations
		CMSUserGroup::setGroupUsers($group->getId(), $form->getFieldset($this->lang('users'))->getWidget('users')->getValues());
		
		// Groups to user relations
		CMSGroupPermission::setGroupPermissions($group->getId(), $form->getFieldset($this->lang('permissions'))->getWidget('permissions')->getValues());
		
		if($form->isSubmitted())
		{
			return $this->Redirect($id,'defaultadmin',$returnid,array('tab' => 'groups'));
		}
	}
}

// Usergroups values
if ($group->getId() != null)
{
	$form->getFieldset($this->lang('users'))->getWidget('users')->setValues(CMSUserGroup::getUsersList($group->getId(), true));
	$form->getFieldset($this->lang('permissions'))->getWidget('permissions')->setValues(CMSGroupPermission::getPermissionsList($group->getId(), true));
}



$smarty->assign('form', $form);
echo $this->ProcessTemplate('admin.group_manage.tpl');
return;

<?php
if(!cmsms()) exit;
if (! $this->CheckAccess()) // Restrict to admin panel and groups with permission
{
	return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	exit;
}

if (isset($params['permission_id']) && $params['permission_id'] != '')
{
	$permission = CMSPermission::retrieveByPk($params['permission_id']);
}

if ((!isset($permission)) || is_null($permission))
{
	$permission = new CMSPermission();
}

$form = new CMSForm($this->GetName(), $id, 'permission_manage',$returnid);
$form->setButtons(array('submit','apply','cancel'));

$form->setWidget('permission_id', 'hidden', array('object' => &$permission, 'field_name' => 'id', 'get_method' => 'getId'));
$form->setWidget('name', 'text', array('object' => &$permission, 'validators' => array('not_empty' => true)));
$form->setWidget('is_active', 'checkbox', array('object' => &$permission));

$form->setFieldset($this->lang('groups'));
$form->getFieldset($this->lang('groups'))->setWidget('groups','select', array('values' => CMSGroup::getGroupList(), 'size' => 10, 'multiple' => true,'expanded' => true));


if ($form->isCancelled())
{
	return $this->Redirect($id,'defaultadmin',$returnid,array('tab' => 'permissions'));
}

if ($form->isPosted())
{
	$form->process();
	
	if(!$form->hasErrors())
	{
		$permission->save();
		
		// Permissions to group relations
		CMSGroupPermission::setPermissionGroups($permission->getId(), $form->getFieldset($this->lang('groups'))->getWidget('groups')->getValues());
	
		
		if($form->isSubmitted())
		{
			return $this->Redirect($id,'defaultadmin',$returnid,array('tab' => 'permissions'));
		}
	}
}

// Grouppermissions values
if ($permission->getId() != null)
{
	$form->getFieldset($this->lang('groups'))->getWidget('groups')->setValues(CMSGroupPermission::getGroupsList($permission->getId(), true));
}



$smarty->assign('form', $form);
echo $this->ProcessTemplate('admin.permission_manage.tpl');
return;

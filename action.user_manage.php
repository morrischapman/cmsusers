<?php
if(!cmsms()) exit;
if (! $this->CheckAccess()) // Restrict to admin panel and users with permission
{
	return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	exit;
}

if (isset($params['user_id']) && $params['user_id'] != '')
{
	$user = CMSUser::retrieveByPk($params['user_id']);
}

if ((!isset($user)) || is_null($user))
{
	$user = new CMSUser();
}

$form = new CMSForm($this->GetName(), $id, 'user_manage',$returnid);
$form->setButtons(array('submit','apply','cancel'));

if ($form->isCancelled())
{
	return $this->Redirect($id,'defaultadmin',$returnid,array('tab' => 'users'));
}
$form->setWidget('user_id', 'hidden', array('object' => &$user, 'field_name' => 'id', 'get_method' => 'getId'));
$form->setWidget('username', 'text', array('object' => &$user, 'validators' => array('not_empty' => true, 'unique' =>'CMSUser::retrieveByUsername')));
$form->setWidget('password', 'password', array('validators' => array('equal_field' => 'confirm_password')));
$form->setWidget('confirm_password', 'password');
$form->setWidget('email', 'text', array('object' => &$user, 'validators' => array('email' => true)));
$form->setWidget('is_active', 'checkbox', array('object' => &$user));
$form->setWidget('is_disabled', 'checkbox', array('object' => &$user));
if ($user->last_login != null)
{
	$last_login = date('Y-m-d H:i:s', strtotime($user->last_login));
}
else
{
	$last_login = $this->lang('never');
}

$form->setWidget('last_login', 'static', array('value' => $last_login));

$form->setFieldset($this->lang('groups'));
$form->getFieldset($this->lang('groups'))->setWidget('groups','select', array('values' => CMSGroup::getGroupList(), 'size' => 10, 'multiple' => true,'expanded' => true));

if (count($user->getPermissions()) > 0)
{
	$form->getFieldset($this->lang('groups'))->setWidget('permissions','static',array('value' => implode(', ', $user->getPermissions())));
}

if ($user->getId() != '')
{
	$form->setWidget('username', 'static', array('value' => $user->username));
	
	$form->setFieldset($this->lang('profile'));
	$form->getFieldset($this->lang('profile'))->setWidget('profile','static', array(
		'value' => $this->CreateLink($id,'profile_manage',$returnid, $this->getIcon('vcard_edit.png', $this->lang('manage users profile')) . ' ' . $this->lang('manage users profile'), array('user_id' => $user->getId()))
		));
		
	if($this->getPreference('profile_module') != '')
	{
		$moduser = $this->getPreference('profile_module');
			if(method_exists(cms_utils::get_module($moduser), 'getUserFunction'))
			{
				$call = cms_utils::get_module($moduser)->getUserFunction();
				$url = call_user_func(array($moduser.'Object', $call), $user->getId(), $id);
				
				$form->getFieldset($this->lang('profile'))->setWidget('profile_module','static', array(
					'value' => '<a href="'.$url.'">'.$this->getIcon('vcard_edit.png', $this->lang('manage users profile')) . ' ' . $this->lang('manage users profile') . '</a>'));
			}
		
	}
}

if ($form->isPosted())
{
	if ($form->getWidget('user_id')->isEmpty())
	{
		$form->getWidget('password')->addValidator('not_empty', array());
	}
		
	$form->process();
	
	if (!$form->hasErrors())
	{
		if (!$form->getWidget('password')->isEmpty())
		{
			$user->setPassword($form->getWidget('password')->getValue());
		}
		
		$user->save();
		$form->getWidget('user_id')->setValues($user->getId());
		
			// Groups to user relations
			CMSUserGroup::setUserGroups($user->getId(), $form->getFieldset($this->lang('groups'))->getWidget('groups')->getValues());
				
		if ($form->isSubmitted())
		{
			return $this->Redirect($id,'defaultadmin',$returnid, array('tab' => 'users'));
		}
		else
		{
			return $this->Redirect($id,'user_manage',$returnid, array('user_id' => $user->getId()));
		}
	}
}

if ($user->getId() != '')
{
	$form->setWidget('username', 'static', array('value' => $user->username));
	$form->getFieldset($this->lang('groups'))->getWidget('groups')->setValues(CMSUserGroup::getGroupsList($user->getId(), true));
}

$smarty->assign('form', $form);
echo $this->ProcessTemplate('admin.user_manage.tpl');
return;
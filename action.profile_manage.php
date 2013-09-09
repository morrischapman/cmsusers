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

if (!is_object($user))
{
		return $this->Redirect($id,'defaultadmin',$returnid,array('tab' => 'users'));
}

$profile = $user->getProfile();

//var_dump($profile);
$fields = $profile->getUserProfileFields();

$form = new CMSForm($this->GetName(),$id,'profile_manage',$returnid);
$form->setButtons(array('submit','apply','cancel'));
$form->setWidget('user_id', 'hidden');

foreach ($fields as $field)
{
	$options = array_merge(
		array('label' => $field->name, 'tips' => $field->tips, 'object' => $profile),
		$field->getOptionsToArray()
		);
	
	//TODO: Treath the field option
	$form->setWidget($field->fname, $field->type, $options);
}

if ($form->isCancelled())
{
		return $this->Redirect($id,'user_manage',$returnid, array('user_id' => $user->getId()));
}

if ($form->isPosted())
{
	$form->process();
	
	if (!$form->hasErrors())
	{		
		$profile->save();
	
		if ($form->isSubmitted())
		{
			return $this->Redirect($id,'user_manage',$returnid, array('user_id' => $user->getId()));
		}
		else
		{
			return $this->Redirect($id,'profile_manage',$returnid, array('user_id' => $user->getId()));
		}
	}
}


$this->smarty->assign('form', $form);
echo $this->ProcessTemplate('admin.profile_manage.tpl');
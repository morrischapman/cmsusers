<?php
if(!cmsms()) exit;

/*
	View an user profile
*/

if (isset($params['username']))
{
	$user = CMSUser::retrieveByUsername($params['username']);
	$this->smarty->assign('user', $user);	
	$this->smarty->assign('profile', $user->getProfile());
	
	echo $this->ProcessTemplateFor('show_success', $params);
}
elseif (isset($params['user_id']))
{
	$user = CMSUser::retrieveByPk($params['user_id']);
	$this->smarty->assign('user', $user);	
	$this->smarty->assign('profile', $user->getProfile());
	
	echo $this->ProcessTemplateFor('show_success', $params);
}
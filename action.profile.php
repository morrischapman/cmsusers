<?php
if(!cmsms()) exit;

	if ($user = CMSUsers::getUser())
	{
		$this->smarty->assign('CMSUser', $user);		
		$this->smarty->assign('profile', $user->getProfile());		
		
		echo $this->ProcessTemplateFor('profile_success', $params);
	}
	else
	{
		return $this->Redirect($id,'signin',$returnid, array('redirect' => 'profile'));
	}
	
	
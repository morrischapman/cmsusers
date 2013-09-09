<?php

if(!cmsms()) exit;

unset($_SESSION['modules']['CMSUsers']['user_id']);

// if (!$form->getWidget('redirect')->isEmpty())
// {
// 	// $this->RedirectContent($id)
// 	return $this->Redirect($id,$form->getWidget('redirect')->getValue(),$returnid);
// }
// else
if($this->GetPreference('signout_redirection') != '')
{
	return CMSUsers::jumpTo($this->GetPreference('signout_redirection'));
}

$this->smarty->assign('success_message', $this->lang('successfully sign out'));
if($this->GetPreference('http_auth') != '')
{
  $this->smarty->assign('success_message', $this->lang('close your browser'));
}
if (($template = $this->GetDefaultTemplate('signout_success'))	&&	($this->GetTemplate($template) !== false))
{
	echo $this->ProcessTemplateFromDatabase($template);
}
else
{
	echo $this->ProcessTemplate('frontend.signout_success.tpl');
}
return;
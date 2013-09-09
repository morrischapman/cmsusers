<?php
if(!cmsms()) exit;

if ($this->getPreference('allow_signin') != 1)
{
	$this->smarty->assign('message', $this->lang('signin disabled'));
	echo $this->ProcessTemplateFor('signin_disabled');
	return;
}

if(isset($_SESSION['modules']['CMSUsers']['user_id']))
{
	// TODO: Change layout
	echo '<p>' . $this->CreateLink($id,'signout',$returnid,$this->lang('signout'),array(),'',false,false,'',false,'user/signout/'.$returnid) . '</p>';
	return;
}

if(isset($params['goback']))
{
	$params['redirect_url'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

$form = new CMSForm($this->GetName(), $id, 'signin', $returnid);
$form->setButtons(array('submit'));
$form->setLabel('submit', $this->lang('signin'));

$form->setWidget('redirect', 'hidden', array('default_value' => isset($params['redirect'])?$params['redirect']:''));
$form->setWidget('redirect_url', 'hidden',array('default_value' => isset($params['redirect_url'])?$params['redirect_url']:''));


if ($this->GetPreference('signup_use_email_as_login') == 1)
{
  $form->setWidget('username','text', array('size' => '40', 'label' => $this->Lang('email')));
}
else
{
  $form->setWidget('username','text', array('size' => '40'));
}




$form->setWidget('password','password', array('size' => '40'));

if ($form->isPosted())
{
	$user = CMSUser::retrieveByUsername($form->getWidget('username')->getValue());
		
	if ($user && $user->authenticate($form->getWidget('password')->getValue()))
	{
		// TODO: Add event
		$_SESSION['modules']['CMSUsers']['user_id'] = $user->getId();
				
		if (!$form->getWidget('redirect')->isEmpty())
		{
			// $this->RedirectContent($id)
			return $this->Redirect($id,$form->getWidget('redirect')->getValue(),$returnid);
		}
		elseif (!$form->getWidget('redirect_url')->isEmpty())
		{
			return CMSUsers::jumpTo($form->getWidget('redirect_url')->getValue());
		}
		elseif($this->GetPreference('signin_redirection') != '')
		{
			return CMSUsers::jumpTo($this->GetPreference('signin_redirection'));
		}
		
		$this->smarty->assign('user', $user);
		$this->smarty->assign('success_message', $this->lang('successfully sign in'));
		echo $this->ProcessTemplateFor('signin_success');
		return;
		
	}
	elseif($user && $user->is_active == 0)
	{
		$form->setError($this->lang('user inactive'));
	}
	elseif($user && $user->is_disabled == 1)
	{
		$form->setError($this->lang('user disabled'));
	}
	else
	{
		$form->setError($this->lang('invalid credentials'));
	}
	
}

$this->smarty->assign('allow_signup', $this->getPreference('allow_signup'));
$this->smarty->assign('title', $this->lang('signin'));
$this->smarty->assign('form', $form);

echo $this->ProcessTemplateFor('signin_form', $params);
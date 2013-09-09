<?php
if(!cmsms()) exit;

/*
	View all users
*/

$query = array();

if (!isset($params['show_all_users']))
{
	$query['where']['is_active'] = 1;
	$query['where']['is_disabled'] = 0;
}

$users = CMSUser::doSelect($query);

// JSON
if(isset($_REQUEST['json']))
{	
	$json = array();
	foreach($users as $user)
	{
		$json[] = $user->getAsArray();
	}
	
	$callback = $_REQUEST['callback'];
	if ($callback) {
		header('Content-type: text/javascript');
	  echo $callback . '(' . utf8_encode(json_encode($json)) . ');';
	} else {		
		header('Content-type: application/x-json');
		echo utf8_encode(json_encode($json));
	}
	exit;
	die();
}


$this->smarty->assign('users', $users);
echo $this->ProcessTemplateFor('user_list_success', $params);
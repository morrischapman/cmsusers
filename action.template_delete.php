<?php
if(!cmsms()) exit;
if (! $this->CheckAccess()) // Restrict to admin panel and users with permission
{
	return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	exit;
}

if (isset($params['template']) && !empty($params['template'])) {
    $this->DeleteTemplate($params['template']);
		$this->RemoveDefaultTemplate($params['template']);
}

$this->Redirect($id, 'defaultadmin', $returnid, array('tab' => 'templates'));
exit;

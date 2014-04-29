<?php
if(!cmsms()) exit;
	// 
	// echo '<pre>';
	// var_dump($_SERVER);
	// echo '</pre>';

	if ($user = CMSUsers::getUser())
	{
		return $this->Redirect($id,'profile',$returnid);
    }

	echo $this->ProcessTemplateFor('default', $params);


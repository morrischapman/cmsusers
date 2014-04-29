<?php
if (!cmsms()) exit;
if (!$this->CheckAccess()) // Restrict to admin panel and users with permission
{
    return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
    exit;
}

/*
    Users admin pannel
*/
$tab = '';
if (isset($params['tab'])) $tab = $params['tab'];

$this->smarty->assign('tab_headers', $this->StartTabHeaders() .
    $this->SetTabHeader('users', $this->Lang('users'), ($tab == 'users') ? true : false) .
    $this->SetTabHeader('groups', $this->Lang('groups'), ($tab == 'groups') ? true : false) .
    $this->SetTabHeader('permissions', $this->Lang('permissions'), ($tab == 'permissions') ? true : false) .
    $this->SetTabHeader('profile_fields', $this->Lang('profile_fields'), ($tab == 'profile_fields') ? true : false) .
    $this->SetTabHeader('templates', $this->Lang('templates'), ($tab == 'templates') ? true : false) .
    $this->SetTabHeader('settings', $this->Lang('settings'), ($tab == 'settings') ? true : false) .
//	$this->SetTabHeader('impex',$this->Lang('impex'),($tab == 'impex')?true:false).

    $this->EndTabHeaders() . $this->StartTabContent());

$this->smarty->assign('end_tab', $this->EndTab());
$this->smarty->assign('tab_footers', $this->EndTabContent());

$this->smarty->assign('start_users_tab', $this->StartTab('users'));
$this->smarty->assign('start_groups_tab', $this->StartTab('groups'));
$this->smarty->assign('start_permissions_tab', $this->StartTab('permissions'));
$this->smarty->assign('start_profile_fields_tab', $this->StartTab('profile_fields'));
$this->smarty->assign('start_templates_tab', $this->StartTab('templates'));
$this->smarty->assign('start_settings_tab', $this->StartTab('settings'));
//$this->smarty->assign('start_impex_tab',$this->StartTab('impex'));

// USERS

$users = CMSUser::doSelect();
$users_actions = array();
foreach ($users as $key => $user) {
    $users_actions[$key]['edit'] = $this->CreateLink($id, 'user_manage', $returnid, $user->username, array('user_id' => $user->getId()));
    $users_actions[$key]['edit_icon'] = $this->CreateLink($id, 'user_manage', $returnid, $this->getIcon('user_edit.png', $this->lang('edit')), array('user_id' => $user->getId()));
    $users_actions[$key]['delete_icon'] = $this->CreateLink($id, 'user_options', $returnid, $this->getIcon('user_delete.png', $this->lang('delete')), array('user_id' => $user->getId(), 'maction' => 'delete'), $this->lang('are_you_sure'));

    if ($user->is_active) {
        $users_actions[$key]['active_icon'] = $this->CreateLink($id, 'user_options', $returnid, $this->getIcon('user_green.png', $this->lang('disable')), array('user_id' => $user->getId(), 'maction' => 'change_status'));
    } else {
        $users_actions[$key]['active_icon'] = $this->CreateLink($id, 'user_options', $returnid, $this->getIcon('user_red.png', $this->lang('enable')), array('user_id' => $user->getId(), 'maction' => 'change_status'));
    }


}
$this->smarty->assign('users', $users);
$this->smarty->assign('users_title', $this->lang('users'));
$this->smarty->assign('users_actions', $users_actions);

$this->smarty->assign('add_user', $this->CreateLink($id, 'user_manage', $returnid,
    $this->getIcon('user_add.png', $this->lang('add_user')) . ' ' . $this->lang('add_user')
));

$this->smarty->assign('last_login', $this->lang('last login'));
$this->smarty->assign('email', $this->lang('email'));

$this->smarty->assign('users_list', $this->ProcessTemplate('admin.users.tpl'));

// GROUPS

$groups = CMSGroup::doSelect();
$groups_actions = array();
foreach ($groups as $key => $group) {
    $groups_actions[$key]['edit'] = $this->CreateLink($id, 'group_manage', $returnid, $group->name, array('group_id' => $group->getId()));
    $groups_actions[$key]['edit_icon'] = $this->CreateLink($id, 'group_manage', $returnid, $this->getIcon('group_edit.png', $this->lang('edit')), array('group_id' => $group->getId()));
    $groups_actions[$key]['delete_icon'] = $this->CreateLink($id, 'group_options', $returnid, $this->getIcon('group_delete.png', $this->lang('delete')), array('group_id' => $group->getId(), 'maction' => 'delete'), $this->lang('are_you_sure'));

    if ($group->is_active) {
        $groups_actions[$key]['active_icon'] = $this->CreateLink($id, 'group_options', $returnid, $this->getIcon('flag_green.png', $this->lang('disable')), array('group_id' => $group->getId(), 'maction' => 'change_status'));
    } else {
        $groups_actions[$key]['active_icon'] = $this->CreateLink($id, 'group_options', $returnid, $this->getIcon('flag_red.png', $this->lang('enable')), array('group_id' => $group->getId(), 'maction' => 'change_status'));
    }
}
$this->smarty->assign('groups', $groups);
$this->smarty->assign('groups_title', $this->lang('groups'));
$this->smarty->assign('type', $this->lang('type'));
$this->smarty->assign('groups_actions', $groups_actions);

$this->smarty->assign('add_group', $this->CreateLink($id, 'group_manage', $returnid,
    $this->getIcon('group_add.png', $this->lang('add_group')) . ' ' . $this->lang('add_group')
));

$this->smarty->assign('groups_list', $this->ProcessTemplate('admin.groups.tpl'));

// Permissions

$permissions = CMSPermission::doSelect();
$permissions_actions = array();
foreach ($permissions as $key => $permission) {
    $permissions_actions[$key]['edit'] = $this->CreateLink($id, 'permission_manage', $returnid, $permission->name, array('permission_id' => $permission->getId()));
    $permissions_actions[$key]['edit_icon'] = $this->CreateLink($id, 'permission_manage', $returnid, $this->getIcon('lock_edit.png', $this->lang('edit')), array('permission_id' => $permission->getId()));
    $permissions_actions[$key]['delete_icon'] = $this->CreateLink($id, 'permission_options', $returnid, $this->getIcon('lock_delete.png', $this->lang('delete')), array('permission_id' => $permission->getId(), 'maction' => 'delete'), $this->lang('are_you_sure'));

    if ($permission->is_active) {
        $permissions_actions[$key]['active_icon'] = $this->CreateLink($id, 'permission_options', $returnid, $this->getIcon('lock.png', $this->lang('disable')), array('permission_id' => $permission->getId(), 'maction' => 'change_status'));
    } else {
        $permissions_actions[$key]['active_icon'] = $this->CreateLink($id, 'permission_options', $returnid, $this->getIcon('lock_open.png', $this->lang('enable')), array('permission_id' => $permission->getId(), 'maction' => 'change_status'));
    }
}
$this->smarty->assign('permissions', $permissions);
$this->smarty->assign('permissions_title', $this->lang('permissions'));
$this->smarty->assign('permissions_actions', $permissions_actions);

$this->smarty->assign('add_permission', $this->CreateLink($id, 'permission_manage', $returnid,
    $this->getIcon('lock_add.png', $this->lang('add_permission')) . ' ' . $this->lang('add_permission')
));

$this->smarty->assign('permissions_list', $this->ProcessTemplate('admin.permissions.tpl'));

// ProfileFields

$profile_fields = CMSProfileField::doSelect();
$profile_fields_actions = array();
foreach ($profile_fields as $key => $profile_field) {
    $profile_fields_actions[$key]['edit'] = $this->CreateLink($id, 'profile_field_manage', $returnid, $profile_field->name, array('profile_field_id' => $profile_field->getId()));
    $profile_fields_actions[$key]['edit_icon'] = $this->CreateLink($id, 'profile_field_manage', $returnid, $this->getIcon('vcard_edit.png', $this->lang('edit')), array('profile_field_id' => $profile_field->getId()));
    $profile_fields_actions[$key]['delete_icon'] = $this->CreateLink($id, 'profile_field_options', $returnid, $this->getIcon('vcard_delete.png', $this->lang('delete')), array('profile_field_id' => $profile_field->getId(), 'maction' => 'delete'), $this->lang('are_you_sure'));

    if ($profile_field->is_active) {
        $profile_fields_actions[$key]['active_icon'] = $this->CreateLink($id, 'profile_field_options', $returnid, $this->getIcon('flag_green.png', $this->lang('disable')), array('profile_field_id' => $profile_field->getId(), 'maction' => 'change_status'));
    } else {
        $profile_fields_actions[$key]['active_icon'] = $this->CreateLink($id, 'profile_field_options', $returnid, $this->getIcon('flag_red.png', $this->lang('enable')), array('profile_field_id' => $profile_field->getId(), 'maction' => 'change_status'));
    }
}
$this->smarty->assign('profile_fields', $profile_fields);
$this->smarty->assign('profile_fields_title', $this->lang('profile_fields'));
$this->smarty->assign('profile_fields_actions', $profile_fields_actions);

$this->smarty->assign('add_profile_field', $this->CreateLink($id, 'profile_field_manage', $returnid,
    $this->getIcon('vcard_add.png', $this->lang('add_profile_field')) . ' ' . $this->lang('add_profile_field')
));

$this->smarty->assign('profile_fields_list', $this->ProcessTemplate('admin.profile_fields.tpl'));


// TEMPLATES

$list_templates = $this->ListTemplates();
$templates = array();
foreach ($list_templates as $template) {
    $row = array(
        'titlelink' => $this->CreateLink($id, 'template_edit', $returnid, $template, array('template' => $template), '', false, false, 'class="itemlink"'),
        'deletelink' => $this->CreateLink($id, 'template_delete', $returnid, cmsms()->variables['admintheme']->DisplayImage('icons/system/delete.gif', $this->Lang('delete'), '', '', 'systemicon'), array('template' => $template), $this->lang('are you sure you want to delete this template')),
        'editlink' => $this->CreateLink($id, 'template_edit', $returnid, cmsms()->variables['admintheme']->DisplayImage('icons/system/edit.gif', $template, '', '', 'systemicon'), array('template' => $template))
    );

    if ($this->isDefaultTemplate($template) !== false) {
        $row['default'] = $this->lang('default template for', $this->isDefaultTemplate($template));
    } else {
        $row['default'] = '';
    }

    $templates[] = $row;
}
$this->smarty->assign('templates', $templates);
$this->smarty->assign('add_templates_link', $this->CreateLink($id, 'template_edit', $returnid, $this->Lang('add template')));
$this->smarty->assign('add_templates_icon', $this->CreateLink($id, 'template_edit', $returnid, cmsms()->variables['admintheme']->DisplayImage('icons/system/newobject.gif', $this->Lang('add_item'), '', '', 'systemicon')));

$this->smarty->assign('templates_list', $this->ProcessTemplate('admin.templates.tpl'));

// Settings

$form_settings = new CMSForm($this->GetName(), $id, 'defaultadmin', $returnid); // .'settings'
$form_settings->setLabel('submit', $this->lang('save'));

$form_settings->setFieldset($this->lang('general'));
$form_settings->getFieldset($this->lang('general'))->setWidget('default_page', 'pages', array('default_value' => cmsms()->GetContentOperations()->GetDefaultPageID(), 'preference' => 'default_page'));

// Profile module for users ?
$modules = CMSUsers::getUserableModules();
if (count($modules) > 0) {
    $form_settings->getFieldset($this->lang('general'))->setWidget('profile_module', 'select', array(
        'preference' => 'profile_module',
        'values' => $modules,
        'include_custom' => $this->lang('none')
    ));
}


$form_settings->setFieldset($this->lang('email'));
$form_settings->getFieldset($this->lang('email'))->setWidget('email_from', 'text', array('preference' => 'email_from'));
$form_settings->getFieldset($this->lang('email'))->setWidget('email_address', 'text', array('preference' => 'email_address', 'validators' => array('email' => true)));

// Sign in
$form_settings->setFieldset($this->lang('signin'));
$form_settings->getFieldset($this->lang('signin'))->setWidget('allow_signin', 'checkbox', array('preference' => 'allow_signin'));
$form_settings->getFieldset($this->lang('signin'))->setWidget('password_reset_email_subject', 'text', array('preference' => 'password_reset_email_subject'));
$form_settings->getFieldset($this->lang('signin'))->setWidget('signin_redirection', 'text', array(
    'preference' => 'signin_redirection',
    'tips' => $this->lang('tips_signin_redirection')
));

// Sign up
$form_settings->setFieldset($this->lang('signup'));
$form_settings->getFieldset($this->lang('signup'))->setWidget('allow_signup', 'checkbox', array('preference' => 'allow_signup'));
$form_settings->getFieldset($this->lang('signup'))->setWidget('signup_redirection', 'text', array(
    'preference' => 'signup_redirection',
    'tips' => $this->lang('tips_signup_redirection')
));

$form_settings->getFieldset($this->lang('signup'))->setWidget('signup_email_unique', 'checkbox', array('preference' => 'signup_email_unique'));
$form_settings->getFieldset($this->lang('signup'))->setWidget('signup_generate_password', 'checkbox', array('preference' => 'signup_generate_password'));
$form_settings->getFieldset($this->lang('signup'))->setWidget('signup_use_email_as_login', 'checkbox', array('preference' => 'signup_use_email_as_login'));
$form_settings->getFieldset($this->lang('signup'))->setWidget('signup_automatically_login', 'checkbox', array('preference' => 'signup_automatically_login'));

if (cms_utils::get_module('Captcha')) {
    $form_settings->getFieldset($this->lang('signup'))->setWidget('signup_use_captcha', 'checkbox', array('preference' => 'signup_use_captcha'));
}
$form_settings->getFieldset($this->lang('signup'))->setWidget('send_signup_email', 'checkbox', array('preference' => 'send_signup_email'));
$form_settings->getFieldset($this->lang('signup'))->setWidget('signup_email_subject', 'text', array('preference' => 'signup_email_subject'));
$form_settings->getFieldset($this->lang('signup'))->setWidget('signup_email_unique', 'checkbox', array('preference' => 'signup_email_unique'));
$form_settings->getFieldset($this->lang('signup'))->setWidget('signup_validation', 'select', array(
    'preference' => 'signup_validation',
    'values' => array(
        'none' => $this->lang('none'),
        'admin' => $this->lang('by_admin'),
        'email' => $this->lang('by_email'),
    )
));

$form_settings->getFieldset($this->lang('signup'))->setWidget('validation_email_subject', 'text', array('preference' => 'validation_email_subject'));
$form_settings->getFieldset($this->lang('signup'))->setWidget('default_group_for_signup', 'select', array(
    'preference' => 'default_group_for_signup',
    'values' => CMSGroup::getGroupList()
));

// Sign out

$form_settings->setFieldset($this->lang('signout'));
$form_settings->getFieldset($this->lang('signout'))->setWidget('signout_redirection', 'text', array(
    'preference' => 'signout_redirection',
    'tips' => $this->lang('tips_signout_redirection')
));

$form_settings->setFieldset($this->lang('security'));
// $form_settings->getFieldset($this->lang('security'))->setWidget('authentication_type', 'select', array(
//   'preference' => 'authentication_type',
// ));

$form_settings->getFieldset($this->lang('security'))->setWidget('http_auth', 'checkbox', array(
    'preference' => 'http_auth'
));

$form_settings->getFieldset($this->lang('security'))->setWidget('htpassword_path', 'text', array(
    'preference' => 'htpassword_path',
    'tips' => $this->lang('tips_htpassword_path')
));

$form_settings->getFieldset($this->lang('security'))->setWidget('htpassword_title', 'text', array(
    'preference' => 'htpassword_title',
));

$form_settings->getFieldset($this->lang('security'))->setWidget('ldap_auth', 'checkbox', array(
    'preference' => 'ldap_auth'
));

$form_settings->getFieldset($this->lang('security'))->setWidget('ldap_server_host', 'text', array(
    'preference' => 'ldap_server_host'
));

$form_settings->getFieldset($this->lang('security'))->setWidget('ldap_server_port', 'text', array(
    'preference' => 'ldap_server_port',
    'default_value' => 389
));

$form_settings->getFieldset($this->lang('security'))->setWidget('ldap_base_dn', 'text', array(
    'preference' => 'ldap_base_dn'
));

$form_settings->getFieldset($this->lang('security'))->setWidget('ldap_bind_dn', 'text', array(
    'preference' => 'ldap_bind_dn'
));

$form_settings->getFieldset($this->lang('security'))->setWidget('ldap_bind_password', 'text', array(
    'preference' => 'ldap_bind_password'
));

if ($form_settings->isSent()) {
    $form_settings->process();
    if ($this->getPreference('http_auth', false)) {
        // echo "-- SWITCH ON --";
        // Set on the httpauth
        if (CMSUser::generateHtPasswd()) {
            $this->switchHttpAuth(true);
        }
    } else {
        // echo "-- SWITCH OFF --";
        $this->switchHttpAuth(false);
    }

    return $this->Redirect($id, 'defaultadmin', $returnid, array('tab' => 'settings'));
}

$this->smarty->assign('form_settings', $form_settings);
$this->smarty->assign('settings', $this->ProcessTemplate('admin.settings.tpl'));

echo $this->ProcessTemplate('admin.default.tpl'); //
// echo $_SERVER['HTTP_USER_AGENT'] . "\n\n";
// var_dump(get_browser(null,true));
// var_dump(get_browser($_SERVER['HTTP_USER_AGENT'],true));


return;
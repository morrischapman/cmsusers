<?php
$lang = array(
	
'users' => 'Users',
'edit' => 'Edit',
'delete' => 'Delete',
'save' => 'Save',
'add_user' => 'Add user',
'groups' => 'Groups',
'group' => 'Group',
'permissions' => 'Permissions',
'permission' => 'Permission',
'profile' => 'Profile',
'profiles' => 'Profiles',
'templates' => 'Templates',
'settings' => 'Settings',
'never' => 'Never',
'are_you_sure' => 'Are you sure ?',

'signin' => 'Sign in',
'signout' => 'Sign out',
'signup' => 'Sign up',
'register' => 'Register',
'create my account' => 'Create my account',
'account created' => 'Your account has been created.',
'account created review by admin' => 'Your account has been created but needs to be activated by the website administrator.',
'account created email sent' => 'An email has been sent to &laquo;%s&raquo;. Follow the instructions in this email to activate your account.',
'change password' => 'Change password',
'invalid password' => 'Invalid password',
'wrong password' => 'Wrong password',
'last login' => 'Last login',
'disable' => 'Disable',
'enable' => 'Enable',
'email' => 'Email',
'captcha' => 'Captcha',
'captcha_tips' => 'Please confirm that you are not a script by entering the letters from the image.',
'invalid captcha' => 'The code you have entered do not correspond to the image.',

'general' => 'General',

'add_group' => 'Add group',
'type' => 'Type',

'add_permission' => 'Add permission',

'profile_edit' => 'Edit profile',
'edit profile' => 'Edit profile',
'edit profile for' => 'Edit profile for %s',
'profile successfully edited' => 'Profile successfully edited',
'password_change' => 'Change password',
'password changed successfully' => 'Password changed successfully',

'account details' => 'Account details for %s',
'account validation' => 'Please verify your account %s',
'account reset password' => 'Reset password request for %s',
'password reset for' => 'Password reset for %s',

'invalid credentials' => 'Invalid credentials',
'invalid credentials or user inactive' => 'Invalid credentials or user inactive',
'user inactive' => 'This user is currently inactive. Please contact the site administrator.',
'user disabled' => 'This user is currently disabled. Please contact the site administrator.',
'successfully sign out' => 'Successfully signed out',
'close your browser' => 'You have to close your browser to sign out.',
'successfully sign in' => 'Successfully signed in',

'signup disabled' => 'User sign up is disabled',
'signin disabled' => 'User sign in is disabled',
'none' => 'None',
'by_admin' => 'By an administrator',
'by_email' => 'By email',
'invalid token or user unknown' => 'Invalid token or user unknown.',
'user validated' => 'The user has been validated.',

'password_forgot' => 'Forgot your password?',
'enter registered email' => 'Please enter the email you have used to register.',

'impex' => 'Import / Export',

'profile_field' => 'Profile field',
'profile_fields' => 'Profile fields',
'add_profile_field' => 'Add a profile field',
'manage users profile' => 'Manage user\'s profile',

'form_email_from' => 'Email from',
'form_email_address' => 'Email address',
'form_validation_email_subject' => 'Validation email subject',
'form_send_signup_email' => 'Send sign up email',
'form_signup_email_subject' => 'Sign up email subject',
'form_password_reset_email_subject' => 'Forgotten password email subject',

'form_profile_module' => 'Module for profile',

'form_name' => 'Name',
'form_type' => 'Type',
'form_username' => 'Username',
'form_current_password' => 'Current password',
'form_new_password' => 'New password',
'form_password' => 'Password',
'form_confirm_password' => 'Confirm password',
'form_email' => 'Email',
'form_is_active' => 'Is active',
'form_is_disabled' => 'Is disabled',
'form_template' => 'Template',
'form_restore_template_from' => 'Restore template from',
'form_code' => 'Code',
'form_default_template_for' => 'Default template for',
'form_users' => 'Users',
'form_groups' => 'Groups',
'form_permissions' => 'Permissions',
'form_tips' => 'Tips',
'form_options' => 'Options',
'form_profile' => 'Profile',
'form_captcha' => 'Captcha',

'form_last_login' => 'Last login',
'form_allow_signup' => 'Allow users to sign up',
'form_allow_signin' => 'Allow users to sign in',

'form_signin_redirection' => 'Sign in redirection',
'tips_signin_redirection' => 'Choose the page alias, a full url starting with "http://" or left blank for a redirection after sign in',


'form_signup_redirection' => 'Sign up redirection',
'tips_signup_redirection' => 'Choose the page alias, a full url starting with "http://" or left blank for a redirection after sign up',

'form_signout' => 'Sign out',
'form_signout_redirection' => 'Sign out redirection',
'tips_signout_redirection' => 'Choose the page alias, a full url starting with "http://" or left blank for a redirection after sign out',

'form_signup_generate_password' => 'Automatically generate the password',
'form_signup_use_email_as_login' => 'Use email as login',
'form_signup_automatically_login' => 'Automatically login after signup',
'form_signup_email_unique' => 'Email must be unique',
'form_signup_use_captcha' => 'Use Captcha',
'form_signup_validation' => 'Sign up validation system',
'form_default_group_for_signup' => 'Default group',
'form_default_page' => 'Default page for operations',

'security' => 'Security',
'form_authentication_type' => 'Authentication type',
'form_http_auth' => 'Activate HTTP Authentication',
'form_htpassword_title' => 'Title for the HTTP Authentication',
'authentication_title' => 'Protected area: Enter your credential',
'form_htpassword_path' => '.htpassword path',
'tips_htpassword_path' => 'This path should be writable by CMS Made Simple.',

'are you sure you want to delete this template' => 'Are you sure you want to delete this template?',
'add template' => 'Add template',
'select one' => 'Select one',
'default template for' => 'default template for "%s" action',

'help' => '
<h3>Frontend actions</h3>
<ul>
	<li><strong>url_for</strong> output an url for an existing action given by the maction param</li>
	<li><strong>link_for</strong> output a link for an existing action given by the maction param</li>
	<li><strong>signin</strong> Display the signin form</li>
	<li><strong>signup</strong> Display the signup form</li>
	<li><strong>signout</strong> Sign out frontend user</li>
	<li><strong>profile</strong> Show the user profile</li>
	<li><strong>profile_edit</strong> Edit the user profile</li>
	<li><strong>password_change</strong> Link to change password</li>
	<li><strong>list</strong> Get users list. Optional: param show_all_users will show all users</li>
	<li><strong>user_show</strong> Require: username - Will show the profile of a given user</li>
</ul>

<h3>url_for</h3>
<p>This action allows you to generate an url for a specific action (specified with the param "maction"). All the given params are transmitted to the action. ex: {CMSUsers action="url_for" maction="signin"}</p>

<h3>link_to</h3>
<p>This action generate a link like url_for but pre formatted. You can specify the title with the param "title".</p>

<h3>Custom content</h3>
<p>You can serve custom content to your users. Check the following code:</p>
<pre>
{if isset($CMSUser)}
<p>User is logged in</p>
{if $CMSUser->checkPermission(4)}<p>Can send newsletters</p>{/if}
{if $CMSUser->checkPermission(\'View extranet\')}<p>Can view extranet</p>{/if}
{if $CMSUser->checkGroup(\'Admin\')}<p>Is in admin group</p>{/if}
{if $CMSUser->checkGroup(1)}<p>Is in admin group</p>{/if}
{/if}
</pre>
',
'uninstalled' => 'Uninstalled successfully',
);
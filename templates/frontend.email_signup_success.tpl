<p>Dear {$user->username},<p>

<p>Thank you for registering at {sitename}. You may now log in to <a href="{$url}">{$url}</a> using the following username:
<p><strong>Your username: </strong> {$user->username}<br />
	 <strong>Your password: </strong> {if isset($password)}{$password}{else}Only you knows it{/if}</p>

<p>Kindest regards,</p>
<p>The Team</p>
<p><a href="{root_url}">{root_url}</a></p>
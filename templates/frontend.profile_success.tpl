{if isset($CMSUser)}
	<h3>Welcome {$CMSUser}!</h3>
	<p><strong>Email: </strong>{$CMSUser->email}</p>
	{foreach from=$profile->getProfileFields() item=value key=name}
	<p><strong>{$name}: </strong>{$value}</p>
	{/foreach}
	<p>{CMSUsers action="link_to" maction="profile_edit" redirect="profile"}</p>
	<p>{CMSUsers action="link_to" maction="password_change" redirect="profile"}</p>
	<p>{CMSUsers action="link_to" maction="signout"}</p>
{/if}
{if isset($user)}
	<h2>Profile of {$user}</h2>
	<p><strong>Email: </strong>{$user->email}</p>
	{foreach from=$profile->getProfileFields() item=value key=name}
	<p><strong>{$name}: </strong>{$value}</p>
	{/foreach}
{/if}
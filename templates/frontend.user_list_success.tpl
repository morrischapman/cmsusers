<ul>
{foreach from=$users item=user}
<li><a href="{CMSUsers action="url_for" maction="user_show" username=$user->username}">{$user->username}</a></li>
{/foreach}
</ul>
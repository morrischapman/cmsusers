{if $users|@count > 0}
<p>{$add_user}</p>
<table cellspacing="0" class="pagetable">
   <thead>
      <tr>
         <th>{$users_title}</th>
		 			<th>{$email}</th>
		 			<th>{$last_login}</th>
         <th class="pageicon" style="width:20px"> </th>
         <th class="pageicon" style="width:20px"> </th>
         <th class="pageicon" style="width:20px"> </th>
      </tr>
   </thead>
   <tbody>
{foreach from=$users item=entry key=id}
		<tr class="{cycle values="row1,row2"}" onmouseover="this.className='{cycle values="row1,row2"}hover';" onmouseout="this.className='{cycle values="row1,row2"}';">
		   <td>{$users_actions[$id].edit}</td>
		   <td>{$entry->email}</td>
		   <td>{$entry->last_login}{*|date_format:"Y-m-d H:i:s"*}</td>
		   <td>{$users_actions[$id].active_icon}</td>
		   <td>{$users_actions[$id].edit_icon}</td>
		   <td>{$users_actions[$id].delete_icon}</td>
		</tr>
{/foreach}
	</tbody>
</table>
{/if}


<p>{$add_user}</p>
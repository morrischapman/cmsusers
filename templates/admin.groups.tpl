{if $groups|@count > 0}
<table cellspacing="0" class="pagetable">
   <thead>
      <tr>
         <th>{$groups_title}</th>
				 <th>{**}</th>
				 <th>{$type}</th>
         <th class="pageicon" style="width:20px"> </th>
         <th class="pageicon" style="width:20px"> </th>
         <th class="pageicon" style="width:20px"> </th>
      </tr>
   </thead>
   <tbody>
{foreach from=$groups item=entry key=id}
		<tr class="{cycle values="row1,row2"}" onmouseover="this.className='{cycle values="row1,row2"}hover';" onmouseout="this.className='{cycle values="row1,row2"}';">
		   <td>[{$entry->getId()}] {$groups_actions[$id].edit}</td>
	   	 <td>{$entry->countUsers()} user(s)</td>
	   	 <td>{$entry->type}</td>
		   <td>{$groups_actions[$id].active_icon}</td>
		   <td>{$groups_actions[$id].edit_icon}</td>
		   <td>{$groups_actions[$id].delete_icon}</td>
		</tr>
{/foreach}
	</tbody>
</table>
{/if}


<p>{$add_group}</p>
{if $permissions|@count > 0}
<table cellspacing="0" class="pagetable">
   <thead>
      <tr>
         <th>{$permissions_title}</th>
         <th>{*$permissions_id*}</th>
         <th class="pageicon" style="width:20px"> </th>
         <th class="pageicon" style="width:20px"> </th>
         <th class="pageicon" style="width:20px"> </th>
      </tr>
   </thead>
   <tbody>
{foreach from=$permissions item=entry key=id}
		<tr class="{cycle values="row1,row2"}" onmouseover="this.className='{cycle values="row1,row2"}hover';" onmouseout="this.className='{cycle values="row1,row2"}';">
		   <td>{$permissions_actions[$id].edit}</td>
			 <td>{$entry->getId()}</td>
		   <td>{$permissions_actions[$id].active_icon}</td>
		   <td>{$permissions_actions[$id].edit_icon}</td>
		   <td>{$permissions_actions[$id].delete_icon}</td>
		</tr>
{/foreach}
	</tbody>
</table>
{/if}


<p>{$add_permission}</p>
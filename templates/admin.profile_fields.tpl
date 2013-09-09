{if $profile_fields|@count > 0}
<table cellspacing="0" class="pagetable">
   <thead>
      <tr>
         <th>{$profile_fields_title}</th>
				 <th>{$type}</th>
				 <th>{**}</th>
         <th class="pageicon" style="width:20px"> </th>
         <th class="pageicon" style="width:20px"> </th>
         <th class="pageicon" style="width:20px"> </th>
      </tr>
   </thead>
   <tbody>
{foreach from=$profile_fields item=entry key=id}
		<tr class="{cycle values="row1,row2"}" onmouseover="this.className='{cycle values="row1,row2"}hover';" onmouseout="this.className='{cycle values="row1,row2"}';">
		   <td>{$profile_fields_actions[$id].edit}</td>
	   	 <td>{$entry->type}</td>	
	   	 <td>In {$entry->countGroups()} group(s)</td>
		   <td>{$profile_fields_actions[$id].active_icon}</td>
		   <td>{$profile_fields_actions[$id].edit_icon}</td>
		   <td>{$profile_fields_actions[$id].delete_icon}</td>
		</tr>
{/foreach}
	</tbody>
</table>
{/if}


<p>{$add_profile_field}</p>
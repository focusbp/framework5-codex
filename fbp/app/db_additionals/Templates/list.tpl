<button class="ajax-link" data-class="{$class}" data-function="add" >{t key="db_additionals.add_button"}</button>
<div style="clear:both;"></div>
<table style="margin-top:20px;">
	<tr>
		<th>{t key="db_additionals.button_title"}</th>
		<th>{t key="db_additionals.action_name"}</th>
		<th>{t key="db_additionals.function_name"}</th>
		<th>{t key="db_additionals.place"}</th>
		<th></th>
	</tr>
	{foreach $items as $d}
		<tr>
			<td>{$d.button_title}</td>
			<td class="code_td">{$d.class_name}</td>
			<td class="code_td">{$d.function_name}</td>
			<td class="code_td">{$place_opt[$d.place]}</td>
			<td>
				<button class="ajax-link listbutton" data-class="{$class}" data-function="delete" data-id="{$d.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>

				<button class="ajax-link listbutton" data-class="{$class}" data-function="edit" data-id="{$d.id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
			</td>
		</tr>
	{/foreach}
</table>

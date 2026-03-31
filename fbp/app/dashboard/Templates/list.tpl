<div>
	<div style="float:right;margin-bottom: 8px;">
		<button class="ajax-link lang" data-class="{$class}" data-function="add">{t key="dashboard.add_button"}</button>
	</div>
</div>
<div style="clear:both;"></div>

<table style="margin-top:10px;" class="moredata">
	<thead>
		<tr class="table-head">
			<th></th>
			<th>{t key="common.class_name"}</th>
			<th>{t key="common.function_name"}</th>
			<th>{t key="dashboard.width"}</th>
			<th></th>
		</tr>
	</thead>
	<tbody class="dashboard_sort">
		{foreach $items as $item}
			<tr id="{$item.id}" class="dragable-item">
				<td><div class="col col_handle"><span class="material-symbols-outlined handle">swap_vert</span></div></td>
				<td class="code_td">{$item.class_name}</td>
				<td class="code_td">{$item.function_name}</td>
				<td>{$column_width_opt[$item.column_width]}</td>
				<td>
					<button class="ajax-link listbutton" data-class="{$class}" data-function="delete" data-id="{$item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>
					<button class="ajax-link listbutton" data-class="{$class}" data-function="edit" data-id="{$item.id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

<script>
$(".dashboard_sort").sortable({
	handle: '.col_handle',
	axis: "y",
	update: function () {
		var log = $(this).sortable("toArray");
		var fd = new FormData();
		fd.append('class', 'dashboard');
		fd.append('function', 'sort');
		fd.append('log', log);
		appcon('app.php', fd);
	}
});
</script>

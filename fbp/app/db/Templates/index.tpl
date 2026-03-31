<div>

	<div style="float:right;margin-bottom: 8px;">
		<button class="ajax-link lang" data-class="{$class}" data-function="add">{t key="db.add_table"}</button>
	</div>
</div>
<div style="clear:both;"></div>

<table style="margin-top:10px;" class="moredata">
	<thead>
		<tr class="table-head">
			<th></th>
			<th class="lang">{t key="db.table_name"}</th>
			<th class="lang">{t key="db.menu_name"}</th>
			<th class="lang">{t key="db.parent_name"}</th>
			<th class="lang">{t key="db.description"}</th>
			<th></th>
		</tr>
	</thead>

	<tbody class="dbsort">
		{foreach $items as $item}
			<tr id="{$item.id}" style="background: #FFF;">
				<td><div class="col col_handle"><span class="material-symbols-outlined handle">swap_vert</span></div></td>

				<td>{$item.tb_name}</td>
				<td>{$item.menu_name}</td>
				<td>{$parents_opt[$item.parent_tb_id]}</td>
				<td>{$item.description}</td>
				<td>
					<button class="ajax-link index-icon" data-class="{$class}" data-function="delete" data-id="{$item.id}" style="float:right;color:black;margin-right:5px;"><span class="material-symbols-outlined">delete</span></button>

					<button class="ajax-link index-icon" data-class="{$class}" data-function="edit" data-id="{$item.id}" data-mode="database" style="float:right;color:black;"><span class="material-symbols-outlined">database</span></button>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

	<script>
	$(".dbsort").sortable({
		handle: '.col_handle',
		axis: "y",
		update: function () {
			var log = $(this).sortable("toArray");
			var fd = new FormData();
			fd.append('class', 'db');
			fd.append('function', 'sort');
			fd.append('log', log);
			appcon('app.php', fd);
		}
	});
		
	</script>

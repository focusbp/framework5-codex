<form id="wizard_public_pages_menu_manage_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.public_pages.menu_manage.description"}</p>
	<input type="hidden" name="menu_order" id="wizard_public_pages_menu_order" value="{foreach $menu_manage_rows as $row}{$row.id}{if !$row@last},{/if}{/foreach}">

	<table class="moredata" style="margin-top:0px;">
		<thead>
			<tr class="table-head">
				<th style="width:5%;"></th>
				<th style="width:27%;">{t key="wizard.public_pages.title"}</th>
				<th style="width:18%;">{t key="wizard.public_pages.menu_manage.show"}</th>
				<th style="width:50%;">{t key="wizard.public_pages.menu_manage.label"}</th>
			</tr>
		</thead>
		<tbody class="wizard-public-pages-menu-sort">
			{foreach $menu_manage_rows as $item}
				<tr id="menu_{$item.id}" style="background:#FFF;">
					<td><span class="ui-icon ui-icon-arrowthick-2-n-s wizard-menu-handle"></span></td>
					<td>{$item.title|escape}</td>
					<td>{html_options name="show_in_menu[`$item.id`]" options=$menu_show_options selected=$item.show_in_menu}</td>
					<td><input type="text" name="menu_label[{$item.id}]" value="{$item.menu_label|default:''|escape}" style="width:100%;"></td>
				</tr>
			{/foreach}
		</tbody>
	</table>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_public_pages_select" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_public_pages_menu_manage_save" data-form="wizard_public_pages_menu_manage_form" style="float:right;">{t key="setting.submit"}</button>
	</div>
</form>

<script>
$(function () {
	var updateOrder = function () {
		var ids = [];
		$(".wizard-public-pages-menu-sort tr").each(function () {
			var rowId = $(this).attr("id") || "";
			rowId = rowId.replace(/^menu_/, "");
			if (rowId !== "") {
				ids.push(rowId);
			}
		});
		$("#wizard_public_pages_menu_order").val(ids.join(","));
	};
	$(".wizard-public-pages-menu-sort").sortable({
		handle: ".wizard-menu-handle",
		axis: "y",
		update: updateOrder
	});
	updateOrder();
});
</script>

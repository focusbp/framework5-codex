<form id="wizard_note_edit_basic_form" onsubmit="return false;">
	<p style="font-size:13px;color:#374151;margin:0 0 10px 0;">{t key="wizard.note_edit.basic.description"}</p>

	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.note_edit.target_note"}</p>
	<p style="margin:0 0 12px 0;color:#111827;">{$row.target_tb_name|escape}</p>

	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.note_edit.note_name"}</p>
	<input type="text" name="menu_name" value="{$row.menu_name|escape}" style="width:100%;">
	<p class="error_message error_menu_name"></p>

	<p style="font-weight:bold;margin:12px 0 4px 0;">{t key="common.description"}</p>
	<input type="text" name="description" value="{$row.description|escape}" style="width:100%;">
	<p class="error_message error_description"></p>

	<div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:12px;">
		<div>
			<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.note_edit.show_menu"}</p>
			{html_options name="show_menu" options=$note_show_menu_options selected=$row.show_menu}
			<p class="error_message error_show_menu"></p>
		</div>
		<div>
			<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.note_edit.sort_order"}</p>
			{html_options name="sort_order" options=$note_sort_order_options selected=$row.sort_order}
			<p class="error_message error_sort_order"></p>
		</div>
		<div>
			<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.note_edit.sortkey"}</p>
			{html_options name="sortkey" options=$note_sortkey_options selected=$row.sortkey}
			<p class="error_message error_sortkey"></p>
		</div>
		<div>
			<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.note_edit.list_width"}</p>
			<input type="text" name="list_width" value="{$row.list_width|escape}" style="width:100%;">
			<p class="error_message error_list_width"></p>
		</div>
		<div>
			<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.note_edit.edit_width"}</p>
			<input type="text" name="edit_width" value="{$row.edit_width|escape}" style="width:100%;">
			<p class="error_message error_edit_width"></p>
		</div>
	</div>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_note_edit_table" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_note_edit_basic_next" data-form="wizard_note_edit_basic_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

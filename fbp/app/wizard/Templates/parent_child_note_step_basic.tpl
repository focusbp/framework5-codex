<form id="wizard_parent_child_note_basic_form" onsubmit="return false;">
	<p style="font-size:13px;color:#374151;margin:0 0 10px 0;">{t key="wizard.parent_child_note.basic.description"}</p>

	<div style="padding:10px;border:1px solid #d5dbe5;background:#f8fafc;margin-bottom:12px;">
		<p style="margin:0 0 4px 0;font-weight:bold;">{t key="wizard.parent_child_note.basic.target"}</p>
		<p style="margin:0;font-size:13px;">{t key="wizard.parent_child_note.basic.parent"}: {$row.parent_tb_name|escape} ({$row.parent_menu_name|escape})</p>
		<p style="margin:4px 0 0 0;font-size:13px;">{t key="wizard.parent_child_note.basic.child"}: {$row.child_tb_name|escape} ({$row.child_menu_name|escape})</p>
	</div>

	<p style="font-weight:bold;margin:0 0 8px 0;">{t key="wizard.parent_child_note.basic.parent_settings"}</p>
	<div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;">
		<div>
			<p style="margin:0 0 4px 0;">{t key="wizard.parent_child_note.basic.display_type"}</p>
			{html_options name="dropdown_item_display_type" options=$dropdown_item_display_type_options selected=$row.dropdown_item_display_type}
			<p class="error_message error_dropdown_item_display_type"></p>
		</div>
		<div>
			<p style="margin:0 0 4px 0;">{t key="wizard.parent_child_note.basic.display_field"}</p>
			{html_options name="dropdown_item" options=$dropdown_item_options selected=$row.dropdown_item}
			<p class="error_message error_dropdown_item"></p>
		</div>
	</div>

	<p style="margin:12px 0 4px 0;">{t key="wizard.parent_child_note.basic.display_template"}</p>
	<input type="text" name="dropdown_item_template" value="{$row.dropdown_item_template|escape}" style="width:100%;" placeholder="{t key='wizard.parent_child_note.basic.display_template_placeholder'}">
	<p class="error_message error_dropdown_item_template"></p>
	<p style="margin:6px 0 0 0;font-size:12px;color:#6b7280;">{t key="wizard.parent_child_note.basic.display_template_help"}</p>

	<p style="font-weight:bold;margin:16px 0 8px 0;">{t key="wizard.parent_child_note.basic.child_settings"}</p>
	<div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;">
		<div>
			<p style="margin:0 0 4px 0;">{t key="wizard.parent_child_note.basic.side_width"}</p>
			<input type="text" name="list_width" value="{$row.list_width|escape}" style="width:100%;">
			<p class="error_message error_list_width"></p>
		</div>
		<div>
			<p style="margin:0 0 4px 0;">{t key="wizard.parent_child_note.basic.cascade_delete"}</p>
			{html_options name="cascade_delete_flag" options=$cascade_delete_flag_options selected=$row.cascade_delete_flag}
			<p class="error_message error_cascade_delete_flag"></p>
		</div>
	</div>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_parent_child_note_child" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_parent_child_note_basic_next" data-form="wizard_parent_child_note_basic_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

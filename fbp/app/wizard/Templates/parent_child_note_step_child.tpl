<form id="wizard_parent_child_note_child_form" onsubmit="return false;">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.parent_child_note.child.description"}</p>
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.parent_child_note.child.label"}</p>
	{html_options name="child_tb_name" options=$child_table_options selected=$row.child_tb_name}
	<p class="error_message error_child_tb_name"></p>
	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_note_select" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_parent_child_note_child_next" data-form="wizard_parent_child_note_child_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

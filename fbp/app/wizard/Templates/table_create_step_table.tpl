<form id="wizard_step_table_form" onsubmit="return false;">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.note_create.note_title.description"}</p>
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.note_create.note_title.label"}</p>
	<input type="text" name="note_title" value="{$row.note_title|escape}" style="width:100%;" placeholder="{t key='wizard.note_create.note_title.placeholder'}" />
	<p class="error_message error_note_title"></p>
	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_purpose" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_table_next" data-form="wizard_step_table_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

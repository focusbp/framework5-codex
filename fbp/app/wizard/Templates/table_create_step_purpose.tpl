<form id="wizard_step_purpose_form" onsubmit="return false;">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.note_create.purpose.description"}</p>
	{if $row.create_mode == "child"}
		<p style="font-size:12px;color:#6b7280;margin:0 0 8px 0;">{t key="wizard.note_create.parent_note"}: {$row.parent_tb_name|escape} ({$row.parent_menu_name|escape})</p>
	{/if}
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.note_create.purpose.label"}</p>
	<textarea name="purpose" rows="4" style="width:100%;" placeholder="{t key='wizard.note_create.purpose.placeholder'}">{$row.purpose|escape}</textarea>
	<p class="error_message error_purpose"></p>
	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="{$back_function|default:'run'|escape}" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_purpose_next" data-form="wizard_step_purpose_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

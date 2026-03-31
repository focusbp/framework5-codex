<form id="wizard_cron_timing_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.cron.timing.description"}</p>
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.cron.timing.label"}</p>
	<textarea name="timing_text" rows="6" style="width:100%;">{$row.timing_text|escape}</textarea>
	<p style="font-size:12px;color:#6b7280;margin:4px 0 0 0;">{t key="wizard.cron.timing.example"}</p>
	<p class="error_message error_timing_text"></p>

	<div style="margin-top:12px;overflow:auto;">
		{if $row.cron_action == 'edit'}
			<button type="button" class="ajax-link" invoke-function="back_to_cron_target" style="float:left;">{t key="common.back"}</button>
		{else}
			<button type="button" class="ajax-link" invoke-function="back_to_cron_select" style="float:left;">{t key="common.back"}</button>
		{/if}
		<button type="button" class="ajax-link" invoke-function="submit_cron_timing_next" data-form="wizard_cron_timing_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

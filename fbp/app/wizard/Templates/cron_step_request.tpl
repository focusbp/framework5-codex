<form id="wizard_cron_request_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.cron.request.description"}</p>
	<table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:8px;">
		<tr>
			<th style="width:180px;text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.cron.timing.label"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{$row.timing_text|escape}</td>
		</tr>
	</table>
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.cron.request.label"}</p>
	<textarea name="request_text" rows="8" style="width:100%;">{$row.request_text|escape}</textarea>
	<p style="font-size:12px;color:#6b7280;margin:4px 0 0 0;">{t key="wizard.cron.request.example"}</p>
	<p class="error_message error_request_text"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_cron_timing" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_cron_request_next" data-form="wizard_cron_request_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

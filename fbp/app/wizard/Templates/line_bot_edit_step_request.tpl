<form id="wizard_line_bot_edit_request_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.line_bot.edit_request.description"}</p>
	<table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:8px;">
		<tr>
			<th style="width:180px;text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.line_bot.edit_request.rule_id"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{$row.rule_id|escape}</td>
		</tr>
		<tr>
			<th style="text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.line_bot.edit_event.action_class"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{$row.action_class|escape}</td>
		</tr>
		<tr>
			<th style="text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.line_bot.edit_request.event"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{$line_bot_event_options[$row.event_type]|escape}</td>
		</tr>
		<tr>
			<th style="text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.line_bot.edit_request.keyword"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{$line_bot_keyword_preview|escape}</td>
		</tr>
	</table>
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.original_form.request.request_text"}</p>
	<textarea name="request_text" rows="8" style="width:100%;">{$row.request_text|escape}</textarea>
	<p class="error_message error_request_text"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_line_bot_edit_event" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_line_bot_edit_request_next" data-form="wizard_line_bot_edit_request_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

<form id="wizard_line_bot_edit_event_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.line_bot.edit_event.description"}</p>
	<table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:8px;">
		<tr>
			<th style="width:180px;text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.line_bot.edit_event.action_class"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{$row.action_class|escape}</td>
		</tr>
	</table>
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.line_bot.event.label"}</p>
	{html_options name="event_type" options=$line_bot_event_options selected=$row.event_type style="width:100%;"}
	<p class="error_message error_event_type"></p>
	<p style="font-weight:bold;margin:12px 0 4px 0;">{t key="wizard.line_bot.keyword.label"}</p>
	<input type="text" name="keyword" value="{$row.keyword|escape}" style="width:100%;">
	<p style="font-size:12px;color:#6b7280;margin:4px 0 0 0;">{t key="wizard.line_bot.edit_event.keyword_help"}</p>
	<p class="error_message error_keyword"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_line_bot_edit_rule" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_line_bot_edit_event_next" data-form="wizard_line_bot_edit_event_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

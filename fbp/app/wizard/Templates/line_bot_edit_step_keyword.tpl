<form id="wizard_line_bot_edit_keyword_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.line_bot.keyword.description"}</p>
	<input type="hidden" name="event_type" value="keyword">
	<p class="error_message error_event_type"></p>
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.line_bot.keyword.label"}</p>
	<input type="text" name="keyword" value="{$row.keyword|escape}" style="width:100%;">
	<p class="error_message error_keyword"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_line_bot_edit_rule" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_line_bot_edit_event_next" data-form="wizard_line_bot_edit_keyword_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

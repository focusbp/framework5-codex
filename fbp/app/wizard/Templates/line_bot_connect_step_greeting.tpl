<form id="wizard_line_bot_connect_greeting_form" onsubmit="return false;">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.line_bot.connect_forward.description"}</p>

	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="setting.line_forward_unknown_to_manager"}</p>
	<select name="line_forward_unknown_to_manager" style="width:100%;">
		{html_options options=$line_forward_unknown_to_manager_options selected=$row.line_forward_unknown_to_manager}
	</select>
	<p class="error_message error_line_forward_unknown_to_manager"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_line_bot_connect_credentials" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_line_bot_connect_save" data-form="wizard_line_bot_connect_greeting_form" style="float:right;">{t key="common.save"}</button>
	</div>
</form>

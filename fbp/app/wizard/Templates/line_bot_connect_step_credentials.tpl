<form id="wizard_line_bot_connect_credentials_form" onsubmit="return false;">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.line_bot.connect_credentials.description"}</p>

	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.line_bot.connect_credentials.channel_secret"}{if $row.line_channel_secret_saved == '1'} <span style="font-size:12px;color:#64748b;">{t key="wizard.common.saved_status"}</span>{/if}</p>
	<input type="password" name="line_channel_secret" value="" placeholder="{if $row.line_channel_secret_saved == '1'}{t key='wizard.common.saved_placeholder'}{/if}" style="width:100%;">
	<p class="error_message error_line_channel_secret"></p>

	<p style="font-weight:bold;margin:12px 0 4px 0;">{t key="wizard.line_bot.connect_credentials.channel_access_token"}{if $row.line_accesstoken_saved == '1'} <span style="font-size:12px;color:#64748b;">{t key="wizard.common.saved_status"}</span>{/if}</p>
	<input type="password" name="line_accesstoken" value="" placeholder="{if $row.line_accesstoken_saved == '1'}{t key='wizard.common.saved_placeholder'}{/if}" style="width:100%;">
	<p class="error_message error_line_accesstoken"></p>
	<div style="margin:12px 0 0 0;font-size:13px;color:#374151;line-height:1.8;">
		<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.line_bot.connect_credentials.steps_title"}</p>
		<p style="margin:0;">{t key="wizard.line_bot.connect_credentials.step1"}</p>
		<p style="margin:0;">{t key="wizard.line_bot.connect_credentials.step2"}</p>
		<p style="margin:0;">{t key="wizard.line_bot.connect_credentials.step3"}</p>
		<p style="margin:0;">{t key="wizard.line_bot.connect_credentials.step4"}</p>
	</div>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_line_bot_connect_response" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_line_bot_connect_credentials_next" data-form="wizard_line_bot_connect_credentials_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

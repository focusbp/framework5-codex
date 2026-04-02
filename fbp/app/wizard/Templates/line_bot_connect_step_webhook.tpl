<div>
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.line_bot.connect_webhook.description"}</p>
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.line_bot.connect_webhook.label"}</p>
	<input type="text" value="{$line_webhook_url|escape}" readonly style="width:100%;">
	<div style="margin:12px 0 0 0;font-size:13px;color:#374151;line-height:1.8;">
		<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.line_bot.connect_webhook.steps_title"}</p>
		<p style="margin:0;">{t key="wizard.line_bot.connect_webhook.step1"}</p>
		<p style="margin:0;">{t key="wizard.line_bot.connect_webhook.step2"}</p>
		<p style="margin:0;">{t key="wizard.line_bot.connect_webhook.step3"}</p>
		<p style="margin:0;">{t key="wizard.line_bot.connect_webhook.step4"}</p>
		<p style="margin:0;">{t key="wizard.line_bot.connect_webhook.step5"}</p>
	</div>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_line_bot_connect_intro" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_line_bot_connect_webhook_next" style="float:right;">{t key="common.next"}</button>
	</div>
</div>

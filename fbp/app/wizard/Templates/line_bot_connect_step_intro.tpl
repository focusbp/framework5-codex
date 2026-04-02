<div>
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.line_bot.connect_intro.description"}</p>
	<p style="margin:0 0 12px 0;">
		<a href="{$line_manager_url|escape}" target="_blank" rel="noopener noreferrer">{t key="wizard.line_bot.connect_intro.link_label"}</a>
	</p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_line_bot_select" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_line_bot_connect_intro_next" style="float:right;">{t key="common.next"}</button>
	</div>
</div>

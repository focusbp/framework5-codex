<form id="wizard_line_bot_edit_rule_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.line_bot.edit_rule.description"}</p>
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.line_bot.edit_rule.label"}</p>
	<select name="rule_id" style="width:100%;">
		{html_options options=$line_bot_edit_rule_options selected=$row.rule_id}
	</select>
	<p class="error_message error_rule_id"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_line_bot_select" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_line_bot_edit_rule_next" data-form="wizard_line_bot_edit_rule_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

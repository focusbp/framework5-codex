<form id="wizard_line_bot_select_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.line_bot.select.description"}</p>
	<div style="line-height:1.9;">
		<label style="display:block;"><input type="radio" name="line_action" value="member_link" {if $row.line_action == 'member_link'}checked{/if}> {t key="wizard.line_bot.action.member_link"}</label>
		<label style="display:block;"><input type="radio" name="line_action" value="connect" {if $row.line_action == 'connect'}checked{/if}> {t key="wizard.line_bot.action.connect"}</label>
		<label style="display:block;"><input type="radio" name="line_action" value="add" {if $row.line_action == 'add'}checked{/if}> {t key="wizard.line_bot.action.add"}</label>
		<label style="display:block;"><input type="radio" name="line_action" value="edit" {if $row.line_action == 'edit'}checked{/if}> {t key="wizard.line_bot.action.edit"}</label>
		<label style="display:block;"><input type="radio" name="line_action" value="delete" {if $row.line_action == 'delete'}checked{/if}> {t key="wizard.line_bot.action.delete"}</label>
	</div>
	<p class="error_message error_line_action"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="run" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_line_bot_action_next" data-form="wizard_line_bot_select_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

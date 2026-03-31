<form id="wizard_cron_select_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.cron.select.description"}</p>
	<div style="line-height:1.9;">
		<label style="display:block;"><input type="radio" name="cron_action" value="add" {if $row.cron_action == 'add'}checked{/if}> {t key="wizard.cron.action.add"}</label>
		<label style="display:block;"><input type="radio" name="cron_action" value="edit" {if $row.cron_action == 'edit'}checked{/if}> {t key="wizard.cron.action.edit"}</label>
		<label style="display:block;"><input type="radio" name="cron_action" value="delete" {if $row.cron_action == 'delete'}checked{/if}> {t key="wizard.cron.action.delete"}</label>
	</div>
	<p class="error_message error_cron_action"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="run" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_cron_action_next" data-form="wizard_cron_select_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

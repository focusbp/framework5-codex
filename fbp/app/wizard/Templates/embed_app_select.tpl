<form id="wizard_embed_app_select_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.embed_app.select.description"}</p>
	<div style="line-height:1.9;">
		<label style="display:block;"><input type="radio" name="embed_action" value="add" {if $row.embed_action == 'add'}checked{/if}> {t key="wizard.embed_app.action.add"}</label>
		<label style="display:block;"><input type="radio" name="embed_action" value="edit" {if $row.embed_action == 'edit'}checked{/if}> {t key="wizard.embed_app.action.edit"}</label>
		<label style="display:block;"><input type="radio" name="embed_action" value="delete" {if $row.embed_action == 'delete'}checked{/if}> {t key="wizard.embed_app.action.delete"}</label>
	</div>
	<p class="error_message error_embed_action"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="run" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_embed_app_action_next" data-form="wizard_embed_app_select_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

<form id="wizard_embed_app_target_form">
	{if $row.embed_action == 'delete'}
		<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.embed_app.target_delete.description"}</p>
	{else}
		<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.embed_app.target.description"}</p>
	{/if}
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.embed_app.target.label"}</p>
	{html_options name="embed_app_id" options=$embed_app_options selected=$row.embed_app_id style="width:100%;"}
	<p class="error_message error_embed_app_id"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_embed_app_select" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_embed_app_target_next" data-form="wizard_embed_app_target_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

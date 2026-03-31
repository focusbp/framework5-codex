<form id="wizard_public_pages_target_form">
	{if $row.page_action == 'delete'}
		<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.public_pages.target_delete.description"}</p>
	{else}
		<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.public_pages.target_edit.description"}</p>
	{/if}
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.public_pages.target.label"}</p>
	{html_options name="registry_id" options=$public_pages_registry_options selected=$row.registry_id style="width:100%;"}
	<p class="error_message error_registry_id"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_public_pages_select" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_public_pages_target_next" data-form="wizard_public_pages_target_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

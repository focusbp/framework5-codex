<form id="wizard_public_pages_select_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.public_pages.select.description"}</p>
	<div style="line-height:1.9;">
		<label style="display:block;"><input type="radio" name="page_action" value="asset_add" {if $row.page_action == 'asset_add'}checked{/if}> {t key="wizard.public_pages.action.asset_add"}</label>
		<label style="display:block;"><input type="radio" name="page_action" value="common_design" {if $row.page_action == 'common_design'}checked{/if}> {t key="wizard.public_pages.action.common_design"}</label>
	</div>
	<p class="error_message error_page_action"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="run" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_public_pages_action_next" data-form="wizard_public_pages_select_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

<form id="wizard_dashboard_target_form">
	{if $row.dashboard_action == 'delete'}
		<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.dashboard.target_delete.description"}</p>
	{else}
		<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.dashboard.target.description"}</p>
	{/if}
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.dashboard.target.label"}</p>
	{html_options name="dashboard_id" options=$dashboard_options selected=$row.dashboard_id style="width:100%;"}
	<p class="error_message error_dashboard_id"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_dashboard_select" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_dashboard_target_next" data-form="wizard_dashboard_target_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

<form id="wizard_dashboard_request_form">
	<table class="moredata" style="margin-top:0px;margin-bottom:12px;">
		{if $row.title|default:'' neq ''}
			<tr><th style="width:30%;">{t key="wizard.dashboard.basic.title"}</th><td>{$row.title|escape}</td></tr>
		{/if}
		<tr><th style="width:30%;">{t key="wizard.dashboard.request.class_name"}</th><td><code style="display:inline;background:#111827;color:#fff;padding:4px 6px;border-radius:4px;font-size:10px;">{$row.class_name|escape}</code></td></tr>
		<tr><th>{t key="wizard.dashboard.request.function_name"}</th><td><code style="display:inline;background:#111827;color:#fff;padding:4px 6px;border-radius:4px;font-size:10px;">{$row.function_name|escape}</code></td></tr>
		<tr><th>{t key="wizard.dashboard.basic.width"}</th><td>{$dashboard_column_width_label|escape}</td></tr>
	</table>

	{if $row.dashboard_action == 'edit'}
		<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.dashboard.request_edit.description"}</p>
	{else}
		<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.dashboard.request_add.description"}</p>
	{/if}
	<textarea name="request_text" style="width:100%;height:180px;">{$row.request_text|default:''|escape}</textarea>
	{if $row.dashboard_action == 'edit'}
		<p style="font-size:12px;color:#6b7280;margin:4px 0 0 0;">{t key="wizard.dashboard.request_edit.example"}</p>
	{else}
		<p style="font-size:12px;color:#6b7280;margin:4px 0 0 0;">{t key="wizard.dashboard.request_add.example"}</p>
	{/if}
	<p class="error_message error_request_text"></p>

	<div style="margin-top:12px;overflow:auto;">
		{if $row.dashboard_action == 'edit'}
			<button type="button" class="ajax-link" invoke-function="back_to_dashboard_target" style="float:left;">{t key="common.back"}</button>
		{else}
			<button type="button" class="ajax-link" invoke-function="back_to_dashboard_basic" style="float:left;">{t key="common.back"}</button>
		{/if}
		<button type="button" class="ajax-link" invoke-function="submit_dashboard_request_next" data-form="wizard_dashboard_request_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

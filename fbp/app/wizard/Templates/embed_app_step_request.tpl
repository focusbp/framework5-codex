<form id="wizard_embed_app_request_form">
	<table class="moredata" style="margin-top:0px;margin-bottom:12px;">
		<tr><th style="width:30%;">{t key="wizard.embed_app.basic.title"}</th><td>{$row.title|escape}</td></tr>
		<tr><th>{t key="wizard.embed_app.basic.class_name"}</th><td><code style="display:inline;background:#111827;color:#fff;padding:4px 6px;border-radius:4px;font-size:10px;">{$row.class_name|escape}</code></td></tr>
	</table>

	{if $row.embed_action == 'edit'}
		<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.embed_app.request_edit.description"}</p>
	{else}
		<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.embed_app.request_add.description"}</p>
	{/if}
	<textarea name="request_text" style="width:100%;height:180px;">{$row.request_text|default:''|escape}</textarea>
	{if $row.embed_action == 'edit'}
		<p style="font-size:12px;color:#6b7280;margin:4px 0 0 0;">{t key="wizard.embed_app.request_edit.example"}</p>
	{else}
		<p style="font-size:12px;color:#6b7280;margin:4px 0 0 0;">{t key="wizard.embed_app.request_add.example"}</p>
	{/if}
	<p class="error_message error_request_text"></p>

	<div style="margin-top:12px;overflow:auto;">
		{if $row.embed_action == 'edit'}
			<button type="button" class="ajax-link" invoke-function="back_to_embed_app_target" style="float:left;">{t key="common.back"}</button>
		{else}
			<button type="button" class="ajax-link" invoke-function="back_to_embed_app_basic" style="float:left;">{t key="common.back"}</button>
		{/if}
		<button type="button" class="ajax-link" invoke-function="submit_embed_app_request_next" data-form="wizard_embed_app_request_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

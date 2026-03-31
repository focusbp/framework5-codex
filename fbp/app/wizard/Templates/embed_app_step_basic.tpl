<form id="wizard_embed_app_basic_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.embed_app.basic.description"}</p>

	<table style="width:100%;border-collapse:collapse;font-size:13px;">
		<tr>
			<th style="width:220px;text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.embed_app.basic.title"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">
				<input type="text" name="title" value="{$row.title|default:''|escape}" style="width:100%;">
				<p class="error_message error_title"></p>
			</td>
		</tr>
		<tr>
			<th style="text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.embed_app.basic.class_name"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">
				<div style="padding:8px 10px;border:1px solid #d5dbe5;background:#f9fafb;border-radius:4px;color:#111827;">
					{t key="wizard.embed_app.basic.class_name_help"} 例: <code style="font-size:11px;">embed_app_contact_widget</code>
				</div>
			</td>
		</tr>
	</table>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_embed_app_select" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_embed_app_basic_next" data-form="wizard_embed_app_basic_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

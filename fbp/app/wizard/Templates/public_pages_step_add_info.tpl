<form id="wizard_public_pages_add_info_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.public_pages.add_info.description"}</p>
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.public_pages.title"}</p>
	<input type="text" name="title" value="{$row.title|default:''|escape}" style="width:100%;">
	<p class="error_message error_title"></p>
	<p style="font-size:12px;color:#6b7280;margin:8px 0 0 0;">{t key="wizard.public_pages.function_name_help"}</p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_public_pages_select" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_public_pages_add_info_next" data-form="wizard_public_pages_add_info_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

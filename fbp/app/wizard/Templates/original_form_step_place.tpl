<form id="wizard_original_form_place_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.original_form.place.description"}</p>
	{if !$is_child}
		<p style="margin:0 0 6px 0;font-size:12px;color:#6b7280;">{t key="wizard.original_form.place.not_child_help"}</p>
	{/if}
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.original_form.place.label"}</p>
	{html_options name="place" options=$place_options selected=$row.place style="width:100%;"}
	<p class="error_message error_place"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_original_form_table" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_original_form_place_next" data-form="wizard_original_form_place_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

<form id="wizard_db_additionals_select_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.db_additionals.select.description"}</p>
	<div style="line-height:1.9;">
		<label style="display:block;"><input type="radio" name="additional_type" value="original_form" {if $row.additional_type == 'original_form'}checked{/if}> Original Form</label>
		<label style="display:block;"><input type="radio" name="additional_type" value="pdf" {if $row.additional_type == 'pdf'}checked{/if}> {t key="wizard.db_additionals.type.pdf"}</label>
		<label style="display:block;"><input type="radio" name="additional_type" value="csv_download" {if $row.additional_type == 'csv_download'}checked{/if}> {t key="wizard.db_additionals.type.csv_download"}</label>
		<label style="display:block;"><input type="radio" name="additional_type" value="csv_upload" {if $row.additional_type == 'csv_upload'}checked{/if}> {t key="wizard.db_additionals.type.csv_upload"}</label>
		<label style="display:block;"><input type="radio" name="additional_type" value="chart" {if $row.additional_type == 'chart'}checked{/if}> {t key="wizard.db_additionals.type.chart"}</label>
		<label style="display:block;"><input type="radio" name="additional_type" value="line_message" {if $row.additional_type == 'line_message'}checked{/if}> {t key="wizard.db_additionals.type.line_message"}</label>
	</div>
	<p class="error_message error_additional_type"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="run" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_db_additionals_type_next" data-form="wizard_db_additionals_select_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

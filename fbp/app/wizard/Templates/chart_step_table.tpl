<form id="wizard_chart_table_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.chart.table.description"}</p>
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.original_form.table.label"}</p>
	{html_options name="db_id" options=$table_id_options selected=$row.db_id style="width:100%;"}
	<p class="error_message error_db_id"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_db_additionals_select" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_chart_table_next" data-form="wizard_chart_table_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

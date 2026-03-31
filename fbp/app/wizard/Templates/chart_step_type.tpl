<form id="wizard_chart_type_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.chart.type.description"}</p>
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.chart.type.label"}</p>
	{html_options name="chart_type" options=$chart_type_options selected=$row.chart_type style="width:100%;"}
	<p class="error_message error_chart_type"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_chart_place" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_chart_type_next" data-form="wizard_chart_type_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

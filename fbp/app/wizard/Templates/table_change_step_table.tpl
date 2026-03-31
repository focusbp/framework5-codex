<form id="wizard_table_change_table_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.table_change.table.description"}</p>
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.table_change.table.label"}</p>
	{html_options name="target_tb_name" options=$table_options selected=$row.target_tb_name style="width:100%;"}
	<p class="error_message error_target_tb_name"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_table_change_select" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_table_change_table_next" data-form="wizard_table_change_table_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

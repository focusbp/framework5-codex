<form id="wizard_table_change_screen_add_preview_form">
	<table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:10px;">
		<tr>
			<th style="width:180px;text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.table_change.preview.change_type"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{t key="wizard.table_change.action.add_screen_field"}</td>
		</tr>
		<tr>
			<th style="text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.table_change.table.label"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{$row.target_tb_name|escape}</td>
		</tr>
		<tr>
			<th style="text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.table_change.screen_add_fields.label"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;white-space:pre-wrap;line-height:1.7;">{$row.fields_text|escape}</td>
		</tr>
		<tr>
			<th style="text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.table_change.preview.display_target"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;white-space:pre-wrap;line-height:1.7;">{$row.display_targets_text|escape}</td>
		</tr>
	</table>

	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.execution_plan"}</p>
	<table style="width:100%;border-collapse:collapse;font-size:12px;">
		<thead>
			<tr>
				<th style="width:56px;border:1px solid #d5dbe5;background:#f4f7fb;padding:6px;">No</th>
				<th style="border:1px solid #d5dbe5;background:#f4f7fb;padding:6px;text-align:left;">{t key="wizard.content"}</th>
			</tr>
		</thead>
		<tbody>
			{foreach $plan_lines as $line}
				<tr>
					<td style="border:1px solid #d5dbe5;padding:6px;text-align:center;">{$line@iteration}</td>
					<td style="border:1px solid #d5dbe5;padding:6px;">{$line|escape}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_table_change_display" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="open_codex_terminal_with_prompt" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

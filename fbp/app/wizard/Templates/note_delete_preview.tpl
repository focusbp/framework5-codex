<form id="wizard_note_delete_preview_form" onsubmit="return false;">
	<table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:10px;">
		<tr>
			<th style="width:180px;text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.note_edit.target_note"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{$row.target_tb_name|escape}</td>
		</tr>
		<tr>
			<th style="text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.note_edit.note_name"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{$row.menu_name|escape}</td>
		</tr>
		<tr>
			<th style="text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="common.description"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{$row.description|escape|default:"-"}</td>
		</tr>
	</table>

	<div style="padding:12px;border:1px solid #fca5a5;background:#fef2f2;color:#991b1b;margin-bottom:12px;">
		{t key="wizard.note_delete.preview.warning"}
	</div>

	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.execution_plan"}</p>
	<table style="width:100%;border-collapse:collapse;font-size:12px;">
		<tbody>
			{foreach $plan_lines as $line}
				<tr>
					<td style="width:56px;border:1px solid #d5dbe5;padding:6px;text-align:center;">{$line@iteration}</td>
					<td style="border:1px solid #d5dbe5;padding:6px;">{$line|escape}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_note_delete_table" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="open_codex_terminal_with_prompt" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

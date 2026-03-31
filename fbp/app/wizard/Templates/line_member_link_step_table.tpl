<div id="wizard_line_member_link_template_area">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.line_bot.member_link.description"}</p>
	<table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:8px;">
		<tr>
			<th style="width:220px;text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.line_bot.member_link.table_name"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{$row.tb_name|escape}</td>
		</tr>
		<tr>
			<th style="text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.line_bot.member_link.line_user_id"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{$row.user_id_field|escape}</td>
		</tr>
		<tr>
			<th style="text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.line_bot.member_link.display_name"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{$row.display_name_field|escape}</td>
		</tr>
		<tr>
			<th style="text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.line_bot.member_link.name"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{$row.name_field|escape}</td>
		</tr>
		<tr>
			<th style="text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.line_bot.member_link.creation"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{t key="wizard.line_bot.member_link.creation_value"}</td>
		</tr>
	</table>
	<p class="error_message error_line_member_template"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_line_bot_select" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_line_member_link_table_next" style="float:right;">{t key="common.next"}</button>
	</div>
</div>

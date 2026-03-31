<form id="wizard_csv_download_request_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.csv_download.request.description"}</p>
	<table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:8px;">
		<tr>
			<th style="width:180px;text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.original_form.table.label"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{$row.tb_name|escape}</td>
		</tr>
		<tr>
			<th style="text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.original_form.place.label"}</th>
			<td style="border:1px solid #d5dbe5;padding:8px;">{$place_options[$row.place]|escape}</td>
		</tr>
	</table>
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.csv_download.request.fields"}</p>
	<div style="max-height:220px;overflow:auto;border:1px solid #d5dbe5;margin-top:0px;margin-bottom:8px;">
		<table style="width:100%;border-collapse:collapse;font-size:12px;">
			<thead>
				<tr>
					<th style="width:180px;border:1px solid #d5dbe5;background:#f4f7fb;padding:6px;text-align:left;">field_name</th>
					<th style="width:180px;border:1px solid #d5dbe5;background:#f4f7fb;padding:6px;text-align:left;">title</th>
					<th style="border:1px solid #d5dbe5;background:#f4f7fb;padding:6px;text-align:left;">options</th>
				</tr>
			</thead>
			<tbody>
				{if $csv_selected_fields|@count > 0}
					{foreach $csv_selected_fields as $one}
						<tr>
							<td style="border:1px solid #d5dbe5;padding:6px;">{$one.field_name|escape}</td>
							<td style="border:1px solid #d5dbe5;padding:6px;">{$one.title|escape}</td>
							<td style="border:1px solid #d5dbe5;padding:6px;">{$one.options_text|escape}</td>
						</tr>
					{/foreach}
				{else}
					<tr>
						<td colspan="3" style="border:1px solid #d5dbe5;padding:6px;color:#6b7280;">{t key="wizard.csv_download.request.fields_empty"}</td>
					</tr>
				{/if}
			</tbody>
		</table>
	</div>
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.original_form.request.button_title"}</p>
	<input type="text" name="button_title" value="{$row.button_title|escape}" style="width:100%;">
	<p class="error_message error_button_title"></p>
	<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.csv_download.request.request_text"}</p>
	<textarea name="request_text" rows="8" style="width:100%;">{$row.request_text|escape}</textarea>
	<p class="error_message error_request_text"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_csv_download_fields" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_csv_download_request_next" data-form="wizard_csv_download_request_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

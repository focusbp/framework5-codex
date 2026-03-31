<form id="wizard_csv_download_fields_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.csv_download.fields.description"}</p>
	<div style="max-height:360px;overflow:auto;border:1px solid #d5dbe5;padding:8px;">
		{if $csv_field_rows|@count > 0}
			{foreach $csv_field_rows as $one}
				<label style="display:block;line-height:1.8;">
					<input type="checkbox" name="field_ids[]" value="{$one.id|escape}" {if $one.selected}checked{/if}>
					{$one.title|escape} [{$one.field_name|escape}]
				</label>
			{/foreach}
		{else}
			<p style="margin:0;color:#6b7280;">{t key="wizard.csv_download.fields.empty"}</p>
		{/if}
	</div>
	<p class="error_message error_field_ids"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_csv_download_place" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_csv_download_fields_next" data-form="wizard_csv_download_fields_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

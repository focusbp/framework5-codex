<form id="wizard_table_change_screen_add_fields_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.table_change.screen_add_fields.description"}</p>
	<div style="max-height:360px;overflow:auto;border:1px solid #d5dbe5;padding:8px;">
		{if $screen_add_field_rows|@count > 0}
			{foreach $screen_add_field_rows as $one}
				<label style="display:block;line-height:1.8;">
					<input type="checkbox" name="screen_add_field_ids[]" value="{$one.id|escape}" {if $one.selected}checked{/if}>
					{$one.label|escape}
				</label>
			{/foreach}
		{else}
			<p style="margin:0;color:#6b7280;">{t key="wizard.table_change.screen_add_fields.empty"}</p>
		{/if}
	</div>
	<p class="error_message error_screen_add_field_ids"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_table_change_table" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_table_change_screen_add_fields_next" data-form="wizard_table_change_screen_add_fields_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

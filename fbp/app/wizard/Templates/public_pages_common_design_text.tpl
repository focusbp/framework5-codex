<form id="wizard_public_pages_common_text_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{$step_prompt|escape}</p>
	<p style="font-size:12px;color:#6b7280;margin:0 0 8px 0;">{$example_prompt|escape}</p>
	<textarea name="step_value" style="width:100%;height:220px;">{$field_value|default:''|escape}</textarea>
	<p class="error_message error_step_value"></p>
	{if count($selected_public_asset_rows) > 0}
		<p style="font-weight:bold;margin:12px 0 6px 0;">{t key="wizard.public_pages.assets.selected"}</p>
		<div style="display:flex;flex-wrap:wrap;gap:10px;">
			{foreach $selected_public_asset_rows as $asset}
				<div style="width:92px;">
					<div style="width:92px;height:92px;border:1px solid #d1d5db;border-radius:4px;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#000;">
						{if $asset.preview_url != ''}<img src="{$asset.preview_url}" style="width:100%;height:100%;object-fit:contain;">{/if}
					</div>
					<div style="margin-top:4px;font-size:10px;line-height:1.3;word-break:break-all;text-align:center;">
						<code style="display:block;background:#111827;color:#fff;padding:4px 6px;border-radius:4px;font-size:10px;">{$asset.asset_key|escape}</code>
					</div>
				</div>
			{/foreach}
		</div>
	{/if}
	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="{$back_function_name|escape}" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="{$next_function_name|escape}" data-form="wizard_public_pages_common_text_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

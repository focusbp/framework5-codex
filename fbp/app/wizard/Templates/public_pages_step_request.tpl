<form id="wizard_public_pages_request_form">
	{if $row.page_action == 'common_design'}
		<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.public_pages.request_common_design.description"}</p>
	{elseif $row.page_action == 'edit'}
		<table class="moredata" style="margin-top:0px;margin-bottom:12px;">
			<tr><th style="width:30%;">{t key="wizard.public_pages.title"}</th><td>{$row.title|escape}</td></tr>
			<tr><th>{t key="wizard.public_pages.function_name"}</th><td><code style="display:inline;background:#111827;color:#fff;padding:4px 6px;border-radius:4px;font-size:10px;">{$row.function_name|escape}</code></td></tr>
			<tr><th>template</th><td>{$row.template_name|escape}</td></tr>
		</table>
		<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.public_pages.request_edit.description"}</p>
	{else}
		<table class="moredata" style="margin-top:0px;margin-bottom:12px;">
			<tr><th style="width:30%;">{t key="wizard.public_pages.title"}</th><td>{$row.title|escape}</td></tr>
			<tr><th>{t key="wizard.public_pages.function_name"}</th><td><code style="display:inline;background:#111827;color:#fff;padding:4px 6px;border-radius:4px;font-size:10px;">{$row.function_name|escape}</code></td></tr>
			<tr><th>template</th><td>{$row.template_name|escape}</td></tr>
		</table>
		<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.public_pages.request_add.description"}</p>
	{/if}

	<textarea name="request_text" style="width:100%;height:180px;">{$row.request_text|default:''|escape}</textarea>
	<p class="error_message error_request_text"></p>

	{if count($selected_public_asset_rows) > 0}
		<p style="font-weight:bold;margin:12px 0 6px 0;">{t key="wizard.public_pages.assets.selected_public_assets"}</p>
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
		<button type="button" class="ajax-link" invoke-function="back_to_public_pages_assets" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_public_pages_request_next" data-form="wizard_public_pages_request_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

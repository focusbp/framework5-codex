<form id="wizard_public_pages_asset_add_form" onsubmit="return false;">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.public_pages.asset_add.description"}</p>
	<table class="custom_events_table">
		<tbody>
			<tr>
				<td style="width:30%;">{t key="wizard.public_pages.asset_add.image_file"}</td>
				<td>
					<input type="file" name="asset_file" accept="image/*">
					<p style="font-size:12px;color:#6b7280;margin:4px 0 0 0;">{t key="wizard.public_pages.asset_add.asset_key_help"}</p>
					<p class="error_message error_asset_file"></p>
				</td>
			</tr>
		</tbody>
	</table>
	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_public_pages_select" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_public_pages_asset_add_exe" data-form="wizard_public_pages_asset_add_form" style="float:right;">{t key="common.add"}</button>
	</div>
</form>

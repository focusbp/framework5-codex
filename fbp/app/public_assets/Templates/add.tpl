<form id="public_assets_add_form" onsubmit="return false;">
	<table class="custom_events_table">
		<tbody>
			<tr><td style="width:30%;">{t key="public_assets.image_file"}</td><td><input type="file" name="asset_file" accept="image/*"><p style="font-size:12px;color:#6b7280;margin:4px 0 0 0;">{t key="public_assets.asset_key_help"}</p><p class="error_message error_asset_file"></p></td></tr>
			<tr><td>{t key="common.status"}</td><td>{html_options name="enabled" options=$enabled_opt selected=$post.enabled|default:1}</td></tr>
		</tbody>
	</table>
	<div style="margin-top:10px;">
		<button class="ajax-link" data-class="public_assets" data-function="add_exe" data-form="public_assets_add_form">{t key="common.save"}</button>
	</div>
</form>

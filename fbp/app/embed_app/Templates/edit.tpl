<form id="embed_app_edit_form_{$data.id}">
	<input type="hidden" name="id" value="{$data.id}">
	<table class="custom_events_table">
		<tbody>
			<tr><td style="width:30%;">{t key="common.title"}</td><td><input type="text" name="title" value="{$data.title|escape}"><p class="error_message error_title"></p></td></tr>
			<tr><td>{t key="embed_app.embed_key"}</td><td><input type="text" name="embed_key" value="{$data.embed_key|escape}"><p class="error_message error_embed_key"></p></td></tr>
			<tr><td>{t key="common.class_name"}</td><td><input type="text" name="class_name" value="{$data.class_name|escape}"><p class="error_message error_class_name"></p></td></tr>
			<tr><td>{t key="embed_app.allowed_origins"}</td><td><textarea name="allowed_origins" style="height:60px;">{$data.allowed_origins|escape}</textarea></td></tr>
			<tr><td>{t key="common.status"}</td><td>{html_options name="enabled" options=$enabled_opt selected=$data.enabled}</td></tr>
		</tbody>
	</table>
	<div style="margin-top:10px;">
		<button class="ajax-link" data-class="{$class}" data-function="edit_exe" data-form="embed_app_edit_form_{$data.id}">{t key="common.save"}</button>
	</div>
</form>

<form id="embed_app_add_form">
	<input type="hidden" name="id" value="{$post.id|default:''}">
	<table class="custom_events_table">
		<tbody>
			<tr><td style="width:30%;">{t key="common.title"}</td><td><input type="text" name="title" value="{$post.title|default:''|escape}"><p class="error_message error_title"></p></td></tr>
			<tr><td>{t key="embed_app.embed_key"}</td><td><input type="text" name="embed_key" value="{$post.embed_key|default:''|escape}"><p class="error_message error_embed_key"></p></td></tr>
			<tr><td>{t key="common.class_name"}</td><td><input type="text" name="class_name" value="{$post.class_name|default:''|escape}"><p class="error_message error_class_name"></p></td></tr>
			<tr><td>{t key="embed_app.allowed_origins"}</td><td><textarea name="allowed_origins" style="height:60px;">{$post.allowed_origins|default:''|escape}</textarea></td></tr>
			<tr><td>{t key="common.status"}</td><td>{html_options name="enabled" options=$enabled_opt selected=$post.enabled|default:1}</td></tr>
		</tbody>
	</table>
	<div style="margin-top:10px;">
		<button class="ajax-link" data-class="{$class}" data-function="add_exe" data-form="embed_app_add_form">{t key="common.save"}</button>
	</div>
</form>

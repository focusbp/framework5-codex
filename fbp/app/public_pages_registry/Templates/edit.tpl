<form id="public_pages_registry_edit_form" onsubmit="return false;">
	<input type="hidden" name="id" value="{$data.id|default:''}">
	<input type="hidden" name="function_name" value="{$data.function_name|default:''|escape}">
	<table class="custom_events_table">
		<tbody>
			<tr><td style="width:30%;">{t key="common.title"}</td><td><input type="text" name="title" value="{$data.title|default:''|escape}"><p class="error_message error_title"></p></td></tr>
			<tr><td>{t key="public_pages_registry.show_in_menu"}</td><td>{html_options name="show_in_menu" options=$menu_opt selected=$data.show_in_menu|default:0}</td></tr>
			<tr><td>{t key="public_pages_registry.menu_label"}</td><td><input type="text" name="menu_label" value="{$data.menu_label|default:''|escape}"></td></tr>
			<tr><td>{t key="common.status"}</td><td>{html_options name="enabled" options=$enabled_opt selected=$data.enabled|default:1}</td></tr>
		</tbody>
	</table>
	<div style="margin-top:10px;">
		<button class="ajax-link" data-class="{$class}" data-function="edit_exe" data-form="public_pages_registry_edit_form">{t key="common.save"}</button>
	</div>
</form>

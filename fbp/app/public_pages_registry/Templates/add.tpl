<form id="public_pages_registry_add_form" onsubmit="return false;">
	<table class="custom_events_table">
		<tbody>
			<tr><td style="width:30%;">{t key="common.title"}</td><td><input type="text" name="title" value="{$post.title|default:''|escape}"><p class="error_message error_title"></p></td></tr>
			<tr><td>{t key="common.function_name"}</td><td><input type="text" name="function_name" value="{$post.function_name|default:''|escape}"><p class="error_message error_function_name"></p></td></tr>
			<tr><td>{t key="public_pages_registry.show_in_menu"}</td><td>{html_options name="show_in_menu" options=$menu_opt selected=$post.show_in_menu|default:0}</td></tr>
			<tr><td>{t key="public_pages_registry.menu_label"}</td><td><input type="text" name="menu_label" value="{$post.menu_label|default:''|escape}"></td></tr>
			<tr><td>{t key="public_pages_registry.menu_sort"}</td><td><input type="text" name="menu_sort" value="{$post.menu_sort|default:''|escape}"><p class="error_message error_menu_sort"></p></td></tr>
			<tr><td>{t key="common.status"}</td><td>{html_options name="enabled" options=$enabled_opt selected=$post.enabled|default:1}</td></tr>
		</tbody>
	</table>
	<div style="margin-top:10px;">
		<button class="ajax-link" data-class="{$class}" data-function="add_exe" data-form="public_pages_registry_add_form">{t key="common.save"}</button>
	</div>
</form>

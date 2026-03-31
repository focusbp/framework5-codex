<form id="public_pages_registry_delete_form" onsubmit="return false;">
	<input type="hidden" name="id" value="{$data.id|default:''}">
	<p>{t key="public_pages_registry.delete_confirm"}</p>
	<table class="custom_events_table">
		<tbody>
			<tr><td style="width:35%;">{t key="common.title"}</td><td>{$data.title|default:''|escape}</td></tr>
			<tr><td>{t key="common.function_name"}</td><td>{$data.function_name|default:''|escape}</td></tr>
		</tbody>
	</table>
	<div style="margin-top:10px;">
		<button class="ajax-link" data-class="{$class}" data-function="delete_exe" data-form="public_pages_registry_delete_form">{t key="common.delete"}</button>
	</div>
</form>

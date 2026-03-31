<p>{t key="embed_app.delete_confirm"}</p>
<p><strong>{$data.title|escape}</strong> ({$data.embed_key|escape})</p>

<form id="embed_app_delete_form_{$data.id}">
	<input type="hidden" name="id" value="{$data.id}">
</form>

<button class="ajax-link" data-class="{$class}" data-function="delete_exe" data-form="embed_app_delete_form_{$data.id}">{t key="common.delete"}</button>

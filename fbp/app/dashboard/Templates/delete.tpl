<form>
	<input type="hidden" name="id" value="{$data.id}">
	<span>{t key="dashboard.delete_label"}</span>
	<p><b>{$data.class_name} / {$data.function_name}</b></p>
	<br>
	<p>{t key="dashboard.delete_confirm"}</p>
	<button class="ajax-link lang" data-class="{$class}" data-function="delete_exe">{t key="common.delete"}</button>
</form>

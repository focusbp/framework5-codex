<form id="dbs_db_fields_delete_form_{$data.id}">

	<input type="hidden" name="id" value="{$data.id}">

	<span class="lang">{t key="db.delete_field_label"}</span>
	<p>
		<b>

			{$data.parameter_name}
		</b>
	</p>

	<br>
	<p class="lang">{t key="db.delete_confirm"}</p>
</form>

<button class="ajax-link lang" data-form="dbs_db_fields_delete_form_{$data.id}" data-class="{$class}" data-function="delete_fiedls_exe">{t key="common.delete"}</button>

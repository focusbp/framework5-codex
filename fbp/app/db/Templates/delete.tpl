<form id="dbs_db_delete_form_{$data.id}">

	<input type="hidden" name="id" value="{$data.id}">

	<span class="lang">{t key="db.delete_table_label"}</span>
	<p>
		<b>

			{$data.tb_name}
		</b>
	</p>

	<br>
	<p class="lang">{t key="db.delete_confirm"}</p>
</form>

<button class="ajax-link lang" data-form="dbs_db_delete_form_{$data.id}" data-class="{$class}" data-function="delete_exe">{t key="common.delete"}</button>

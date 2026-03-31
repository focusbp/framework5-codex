<form id="dbs_db_add_form">

	<div>
		<p class="lang">{t key="db.table_name_help"}</p>
		<input type="text" name="tb_name" value="{$post.tb_name}">
		<p class="error lang">{$errors['tb_name']}</p>
	</div>

	<div>
		<p class="lang">{t key="db.parent_table"}</p>
		{html_options name="parent_tb_id" options=$parents_opt selected=$post["parent_tb_id"]}
	</div>


	<div>
		<button class="ajax-link lang" data-form="dbs_db_add_form" data-class="{$class}" data-function="add_exe">{t key="common.add"}</button>
	</div>
</form>

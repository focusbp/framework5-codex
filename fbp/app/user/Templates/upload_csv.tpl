<form id="upload_csv_form">
	
        <p>{t key="user.csv.choose_file"}</p>
	<input type="file" name="users_csv" class="fr_image_paste">
	<p class="error">{$errors['users_csv']}</p>
	<p>{t key="user.csv.format_help"}</p>
	<img src="app.php?class={$class}&function=image_sample" style="width:40%">
	
	<p>{t key="user.csv.character_code"}</p>
	{html_options name="code" options=$code_list selected=$post.code}

	<button class="ajax-link" data-form="upload_csv_form" data-class="{$class}" data-function="upload_csv_confirm">{t key="common.update"}</button>
	
	<div style="height:100px;"></div>
</form>

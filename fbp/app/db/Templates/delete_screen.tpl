<form id="form_{$timestamp}">

	<input type="hidden" name="screen_id" value="{$post.screen_id}">
	<input type="hidden" name="db_id" value="{$post.db_id}">

	<span class="lang">{t key="db.delete_screen_label"}</span>
	<p>
		<b>

			{$data.screen_name}
		</b>
		
	</p>
	<p class="error_message error_screen_name"></p>

	<br>
	<p class="lang">{t key="db.delete_confirm"}</p>
</form>

<button class="ajax-link lang" data-form="form_{$timestamp}" data-class="{$class}" data-function="delete_screen_exe">{t key="common.delete"}</button>

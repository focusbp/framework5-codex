
<form>

	<input type="hidden" name="id" value="{$data.id}">

	<span class="lang">{t key="db_additionals.delete_label"}</span>
	<p>
		<b>
			{$data.button_title}
		</b>
	</p>

	<br>
	<p class="lang">{t key="db_additionals.delete_confirm"}</p>

	<button class="ajax-link lang" data-class="{$class}" data-function="delete_exe" data-reload_db_id="{$reload_db_id}">{t key="common.delete"}</button>
</form>


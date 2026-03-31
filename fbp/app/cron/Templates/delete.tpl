<form id="form_{$timestamp}">

	<input type="hidden" name="id" value="{$data.id}">

	<span class="lang">{t key="cron.delete_label"}</span>
	<p>
		<b>
			{$data.title}
		</b>
	</p>

	<br>
	<p class="lang">{t key="cron.delete_confirm"}</p>
</form>

<button class="ajax-link lang" data-form="form_{$timestamp}" data-class="{$class}" data-function="delete_exe">{t key="common.delete"}</button>

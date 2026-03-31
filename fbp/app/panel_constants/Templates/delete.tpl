<form id="panel_constants_delete_form_{$data.id}">
	<input type="hidden" name="id" value="{$data.id}">
	{t key="panel_constants.delete_confirm" name=$data.array_name assign="panel_constants_delete_confirm_html"}
	<p class="lang">{$panel_constants_delete_confirm_html nofilter}</p>
	<button class="ajax-link lang" data-form="panel_constants_delete_form_{$data.id}" data-class="{$class}" data-function="delete_exe">{t key="panel_constants.delete"}</button>
</form>

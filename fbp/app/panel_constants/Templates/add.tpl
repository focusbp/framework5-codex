<form id="panel_constants_add_form">
	<h6 class="lang">{t key="panel_constants.array_name_help"}</h6>
	<input type="text" name="array_name" value="{$post.array_name|default:''}">
	<p class="error_message lang error_array_name"></p>

	<button class="ajax-link lang" data-form="panel_constants_add_form" data-class="{$class}" data-function="add_exe">{t key="panel_constants.add"}</button>
</form>

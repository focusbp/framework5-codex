<form>
	<div class="form-row">
		<p style="font-weight:bold;">{t key="common.class_name"}</p>
		<input type="text" name="class_name" value="{$post.class_name}">
		<p class="error_message error_class_name"></p>
	</div>

	<div class="form-row">
		<p style="font-weight:bold;">{t key="common.function_name"}</p>
		<input type="text" name="function_name" value="{$post.function_name|default:'dashboard'}">
		<p class="error_message error_function_name"></p>
	</div>

	<div class="form-row">
		<p style="font-weight:bold;">{t key="dashboard.width"}</p>
		{html_options name="column_width" options=$column_width_opt selected=$post.column_width}
		<p class="error_message error_column_width"></p>
	</div>

	<button type="button" class="ajax-link lang" invoke-function="add_exe">{t key="common.save"}</button>
</form>

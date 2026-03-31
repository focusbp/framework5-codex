<form id="form_{$timestamp}">


	<div>
		<p class="lang">{t key="cron.title_help"}</p>
		<input type="text" name="title" value="{$post.title}">
	</div>

	<div>
		<p class="lang">{t key="common.class_name"}</p>
		<input type="text" name="class_name" value="{$post.class_name}">
	</div>

	<div>
		<p class="lang">{t key="cron.handler_function"}</p>
		<input type="text" name="function_name" value="{$post.function_name}">
	</div>

	<div>
		<button class="ajax-link lang" data-form="form_{$timestamp}" data-class="{$class}" data-function="add_exe">{t key="common.add"}</button>
	</div>
</form>

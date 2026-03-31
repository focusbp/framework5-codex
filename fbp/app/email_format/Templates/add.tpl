<form id="email_format_email_format_add_form">


	<div>
		<p class="lang">{t key="email_format.template_name"}:</p>
		<input type="text" name="template_name" value="{$post.template_name}">

		<p class="error lang">{$errors['template_name']}</p>
	</div>
	<div>
		<p class="lang">Key:</p>
		<input type="text" name="key" value="{$post.key}">

		<p class="error lang">{$errors['key']}</p>
	</div>

	<div>
		<button class="ajax-link lang btn_blue_style" data-form="email_format_email_format_add_form" data-class="{$class}" data-function="add_exe">{t key="common.add"}</button>
	</div>
</form>

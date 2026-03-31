<div class="edit_left">

	<form id="email_format_email_format_edit_form_{$data.id}">

		<input type="hidden" name="id" value="{$data.id}">

		<div>
			<p class="lang">{t key="email_format.template_name"}:</p>

			<input type="text" name="template_name" value="{$data.template_name}">
			<p class="error lang">{$errors['template_name']}</p>
		</div>

		<div>
			<p class="lang">Key:</p>

			<input type="text" name="key" value="{$data.key}">
			<p class="error lang">{$errors['key']}</p>
		</div>

		<div>
			<p class="lang">{t key="email_format.subject"}:</p>
			<input type="text" name="subject" value="{$data.subject}">

			<p class="error lang">{$errors['subject']}</p>
		</div>
		<div>
			<p class="lang">{t key="email_format.body"}:</p>

			<textarea name="body" style="font-size:12px;">{$data.body}</textarea>
			<p class="error lang">{$errors['body']}</p>
		</div>
		
		<div>
			{if count($referenced_function) > 0}
			<p>{t key="email_format.references"}</p>
			<ul>
				{foreach $referenced_function as $f}
					<li style="list-style: disc;margin-left: 20px;">{$f.title} <span class="table_name">({$screen_opt[$f.option_screen_id]})</span></li>
				{/foreach}
			</ul>
			{/if}
		</div>

		<div>
			<button class="ajax-link lang" data-form="email_format_email_format_edit_form_{$data.id}" data-class="{$class}" data-function="edit_exe">{t key="common.update"}</button>
		</div>
	</form>
</div>

<div class="emailformat_edit_right">


	
</div>

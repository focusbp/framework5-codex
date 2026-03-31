<form id="email_format_email_format_delete_form_{$data.id}">

	<input type="hidden" name="id" value="{$data.id}">

	<span class="lang">{t key="email_format.delete_label"}</span>
	<p>
		<b>

			{$data.template_name}

		</b>
	</p>
	<br>
	<p class="lang">{t key="email_format.delete_confirm"}</p>
</form>

<div class="flex-full" style="border:none;justify-content:flex-start; margin-top: 15px;margin-bottom: 15px;">
	<button class="cancel_delete lang">{t key="email_format.no"}</button>
	<button class="ajax-link lang" data-form="email_format_email_format_delete_form_{$data.id}" data-class="{$class}" data-function="delete_exe">{t key="email_format.yes"}</button>
</div>
<script>
	$('.cancel_delete').click(function () {
		$(this).parent().closest(".multi_dialog").children('.multi_dialog_title_area').find('.multi_dialog_close').click();
	});
</script>

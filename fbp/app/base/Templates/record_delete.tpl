<form id="record_delete_form_{$data.id}">

	<input type="hidden" name="id" value="{$data.id}">

	<span class="lang">{t key="base.record_delete.label"}</span>
	<p>
		<b>

			{$data.operation_name}

		</b>
	</p>
	<br>
	<p class="lang">{t key="base.record_delete.confirm"}</p>

</form>
<button class="cancel_delete lang">{t key="base.record_delete.no"}</button>
<button class="ajax-link lang" data-form="record_delete_form_{$data.id}" data-class="{$class}" data-function="record_delete_exe">{t key="common.delete"}</button>

<script>
	$('.cancel_delete').click(function () {
		$(this).parent().closest(".multi_dialog").children('.multi_dialog_title_area').find('.multi_dialog_close').click();
	});
</script>

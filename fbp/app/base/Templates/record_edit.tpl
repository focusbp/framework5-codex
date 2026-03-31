<form id="record_edit_form_{$data.id}">
    <input type="hidden" name="record_id" value="{$data.id}">
    <textarea name="json_string" id="myTextArea">{$json}</textarea>

	<div>
		<button class="ajax-link lang" data-form="record_edit_form_{$data.id}" data-class="{$class}"
				data-function="record_edit_exe">{t key="base.record_edit.button"}</button>
	</div>

</form>

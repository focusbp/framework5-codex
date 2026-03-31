<form id="wizard_note_select_form" onsubmit="return false;">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.note.select.description"}</p>
	<div style="line-height:1.9;">
		<label style="display:block;"><input type="radio" name="note_action" value="add" {if $row.note_action == 'add'}checked{/if}> {t key="wizard.note.action.add"}</label>
		<label style="display:block;"><input type="radio" name="note_action" value="update" {if $row.note_action == 'update'}checked{/if}> {t key="wizard.note.action.update"}</label>
		<label style="display:block;"><input type="radio" name="note_action" value="delete" {if $row.note_action == 'delete'}checked{/if}> {t key="wizard.note.action.delete"}</label>
		<label style="display:block;"><input type="radio" name="note_action" value="child_add" {if $row.note_action == 'child_add'}checked{/if}> {t key="wizard.note.action.child_add"}</label>
		<label style="display:block;"><input type="radio" name="note_action" value="parent_child" {if $row.note_action == 'parent_child'}checked{/if}> {t key="wizard.note.action.parent_child"}</label>
	</div>
	<p class="error_message error_note_action"></p>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="run" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_note_action_next" data-form="wizard_note_select_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

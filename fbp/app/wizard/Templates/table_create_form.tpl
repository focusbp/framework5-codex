<form id="wizard_table_create_form">
	<p style="font-size:13px;color:#374151;margin:0 0 10px 0;">{t key="wizard.table_create.form.description"}</p>

	<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
		<div>
			<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.note_create.project.label"}</p>
			<input type="text" name="project_name" value="{$row.project_name|escape}" style="width:100%;" />
		</div>
		<div>
			<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.table_create.form.table_name"}</p>
			<input type="text" name="table_name" value="{$row.table_name|escape}" placeholder="{t key='wizard.table_create.form.table_name_placeholder'}" style="width:100%;" />
			<p class="error_message error_table_name"></p>
		</div>
	</div>

	<div style="margin-top:10px;">
		<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.note_create.menu.label"}</p>
		<input type="text" name="menu_name" value="{$row.menu_name|escape}" placeholder="{t key='wizard.table_create.form.menu_name_placeholder'}" style="width:100%;" />
	</div>

	<div style="margin-top:10px;">
		<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.note_create.purpose.label"}</p>
		<textarea name="purpose" rows="3" style="width:100%;" placeholder="{t key='wizard.note_create.purpose.placeholder'}">{$row.purpose|escape}</textarea>
		<p class="error_message error_purpose"></p>
	</div>

	<div style="margin-top:10px;">
		<p style="font-weight:bold;margin:0 0 4px 0;">{t key="wizard.table_create.form.fields_label"}</p>
		<p style="margin:0 0 4px 0;font-size:12px;color:#6b7280;">{t key="wizard.table_create.form.fields_help"}</p>
		<textarea name="fields_text" rows="6" style="width:100%;">{$row.fields_text|escape}</textarea>
	</div>

	<div style="display:flex;gap:8px;margin-top:12px;">
		<button type="button" class="ajax-link" invoke-function="build_table_create_prompt" data-form="wizard_table_create_form">{t key="wizard.execution_plan"}</button>
		<button type="button" class="ajax-link" invoke-function="run">{t key="wizard.return_to_list"}</button>
		<button type="button" class="ajax-link" invoke-function="close_dialog">{t key="common.close"}</button>
	</div>
</form>

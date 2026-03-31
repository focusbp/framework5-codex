<form id="wizard_step_fields_form" onsubmit="return false;">
	<p style="font-size:13px;color:#374151;margin:0 0 10px 0;">{t key="wizard.note_create.fields.description"}</p>

	<div style="margin-bottom:10px;padding:10px;border:1px solid #d5dbe5;background:#f8fafc;">
		<label style="margin-right:16px;">
			<input type="radio" name="field_mode" value="auto" {if $row.field_mode != "manual"}checked{/if} onchange="var wrap=document.getElementById('wizard_manual_fields_wrap'); if(wrap) wrap.style.display='none';">
			{t key="wizard.note_create.fields.mode_auto"}
		</label>
		<label>
			<input type="radio" name="field_mode" value="manual" {if $row.field_mode == "manual"}checked{/if} onchange="var wrap=document.getElementById('wizard_manual_fields_wrap'); if(wrap) wrap.style.display='';">
			{t key="wizard.note_create.fields.mode_manual"}
		</label>
		<p class="error_message error_field_mode"></p>
	</div>

	<div id="wizard_manual_fields_wrap" {if $row.field_mode != "manual"}style="display:none;"{/if}>
		<div style="display:flex;align-items:flex-end;gap:8px;flex-wrap:wrap;padding:10px;border:1px solid #d5dbe5;background:#f8fafc;">
			<div style="flex:2 1 180px;">
				<p style="margin:0 0 4px 0;font-size:12px;">{t key="wizard.note_create.fields.title"}</p>
				<input type="text" id="wizard_manual_field_title" style="width:100%;" placeholder="{t key='wizard.note_create.fields.title_placeholder'}">
			</div>
			<div style="flex:1 1 120px;">
				<p style="margin:0 0 4px 0;font-size:12px;">{t key="wizard.note_create.fields.type"}</p>
				<select id="wizard_manual_field_type" style="width:100%;" onchange="var wrap=document.getElementById('wizard_manual_field_options_wrap'); var input=document.getElementById('wizard_manual_field_options'); var show=['dropdown','checkbox','radio'].indexOf(this.value)>=0; if(wrap) wrap.style.display=show?'':'none'; if(!show && input) input.value='';">
					{html_options options=$table_create_field_type_options}
				</select>
			</div>
			<div id="wizard_manual_field_options_wrap" style="flex:2 1 220px;display:none;">
				<p style="margin:0 0 4px 0;font-size:12px;">{t key="wizard.note_create.fields.options"}</p>
				<input type="text" id="wizard_manual_field_options" style="width:100%;" placeholder="{t key='wizard.note_create.fields.options_placeholder'}">
			</div>
			<div style="flex:0 0 auto;">
				<button type="button" id="wizard_manual_field_add" style="white-space:nowrap;" onclick="var manualText=document.getElementById('wizard_manual_fields_text'); var titleInput=document.getElementById('wizard_manual_field_title'); var typeSelect=document.getElementById('wizard_manual_field_type'); var optionsWrap=document.getElementById('wizard_manual_field_options_wrap'); var optionsInput=document.getElementById('wizard_manual_field_options'); if(!manualText||!titleInput||!typeSelect) return false; var title=titleInput.value.replace(/\s+/g,' ').trim(); var type=typeSelect.value; var options=optionsInput?optionsInput.value.replace(/\s+/g,' ').trim():''; if(!title) return titleInput.focus(), false; var line=title+'（'+type; if(optionsWrap && optionsWrap.style.display !== 'none' && options !== '') line += ' / ' + options; line += '）'; var current=manualText.value.replace(/\s+$/,''); manualText.value=current===''?line:current+'\n'+line; titleInput.value=''; if(optionsInput) optionsInput.value=''; titleInput.focus(); return false;">{t key="common.add"}</button>
			</div>
					<p style="margin:10px 0 0 0;font-size:12px;color:#6b7280;">{t key="wizard.note_create.fields.example"}</p>
					
		<textarea name="manual_fields_text" id="wizard_manual_fields_text" rows="9" style="width:100%;">{$row.manual_fields_text|escape}</textarea>
		</div>




		<p class="error_message error_manual_fields_text"></p>
	</div>
	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_table" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_fields_next" data-form="wizard_step_fields_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

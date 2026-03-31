<form id="wizard_step_display_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.note_create.display.description"}</p>
	<table style="width:100%;border-collapse:collapse;font-size:13px;">
		<thead>
			<tr>
				<th style="width:220px;text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.table_change.display.field_name"}</th>
				{foreach $display_target_labels as $key => $label}
					<th style="text-align:center;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{$label|escape}</th>
				{/foreach}
			</tr>
		</thead>
		<tbody>
			{foreach $field_display_rows as $fr}
				<tr>
					<td style="border:1px solid #d5dbe5;padding:8px;">{$fr.title|escape}</td>
					{foreach $display_target_labels as $key => $label}
						<td style="text-align:center;border:1px solid #d5dbe5;padding:8px;">
							<input type="checkbox" name="display_matrix[{$fr.idx}][{$key}]" value="1" {if $fr.targets[$key]}checked{/if}>
						</td>
					{/foreach}
				</tr>
			{/foreach}
		</tbody>
	</table>

	<div style="margin-top:12px;overflow:auto;">
		<button type="button" class="ajax-link" invoke-function="back_to_fields" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_create_display_next" data-form="wizard_step_display_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

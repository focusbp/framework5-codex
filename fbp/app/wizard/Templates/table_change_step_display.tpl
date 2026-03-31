<form id="wizard_table_change_display_form">
	<p style="font-size:13px;color:#374151;margin:0 0 8px 0;">{t key="wizard.table_change.display.description"}</p>
	<input type="hidden" name="display_order" id="wizard_display_order" value="">
	<table style="width:100%;border-collapse:collapse;font-size:13px;">
		<thead>
			<tr>
				{if $is_screen_replace_mode}
					<th style="width:72px;text-align:center;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.table_change.display.order"}</th>
				{/if}
				<th style="width:220px;text-align:left;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{t key="wizard.table_change.display.field_name"}</th>
				{foreach $display_target_labels as $key => $label}
					<th style="text-align:center;border:1px solid #d5dbe5;background:#f4f7fb;padding:8px;">{$label|escape}</th>
				{/foreach}
			</tr>
		</thead>
		<tbody>
			{foreach $field_display_rows as $fr}
				<tr class="wizard-display-row" data-row-idx="{$fr.idx}">
					{if $is_screen_replace_mode}
						<td style="text-align:center;border:1px solid #d5dbe5;padding:8px;">
							<div style="display:flex;align-items:center;justify-content:center;gap:6px;">
								<button type="button" class="wizard-sort-up" style="padding:2px 6px;">▲</button>
								<button type="button" class="wizard-sort-down" style="padding:2px 6px;">▼</button>
							</div>
						</td>
					{/if}
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
		<button type="button" class="ajax-link" invoke-function="{$back_function|escape}" style="float:left;">{t key="common.back"}</button>
		<button type="button" class="ajax-link" invoke-function="submit_table_change_display_next" data-form="wizard_table_change_display_form" style="float:right;">{t key="common.next"}</button>
	</div>
</form>

<script>
(function(){
	var form = document.getElementById('wizard_table_change_display_form');
	if (!form) { return; }
	var hidden = document.getElementById('wizard_display_order');
	function updateOrder() {
		if (!hidden) { return; }
		var rows = form.querySelectorAll('tr.wizard-display-row');
		var ids = [];
		rows.forEach(function(row){ ids.push(row.getAttribute('data-row-idx') || ''); });
		hidden.value = ids.join(',');
	}
	form.addEventListener('click', function(ev){
		var up = ev.target.closest('.wizard-sort-up');
		var down = ev.target.closest('.wizard-sort-down');
		if (!up && !down) { return; }
		ev.preventDefault();
		var tr = ev.target.closest('tr.wizard-display-row');
		if (!tr) { return; }
		if (up) {
			var prev = tr.previousElementSibling;
			if (prev && prev.classList.contains('wizard-display-row')) {
				tr.parentNode.insertBefore(tr, prev);
			}
		}
		if (down) {
			var next = tr.nextElementSibling;
			if (next && next.classList.contains('wizard-display-row')) {
				tr.parentNode.insertBefore(next, tr);
			}
		}
		updateOrder();
	});
	updateOrder();
})();
</script>

{if $testserver || $setting.show_developer_panel == 1}
	<div class="db_edit_button_area">
		<button class="ajax-link" invoke-class="db" invoke-function="edit" data-id="{$db_id}"  data-mode="screen" data-screen="edit">
		<span class="material-symbols-outlined">table</span>
		</button>
	</div>
{/if}

<button class="ajax-link" data-form="form_{$timestamp}" data-class="{$class}" data-function="edit_exe" data-db_id="{$db_id}">{t key="common.update"}</button>

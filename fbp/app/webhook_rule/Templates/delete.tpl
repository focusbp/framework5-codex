<form id="webhook_rule_delete_form_{$data.id}">
	<input type="hidden" name="id" value="{$data.id}">
	<p class="lang">{t key="webhook_rule.delete_confirm"}</p>
	<p><strong>{$data.channel} / {$data.keyword}</strong></p>
	<button class="ajax-link lang" data-form="webhook_rule_delete_form_{$data.id}" data-class="{$class}" data-function="delete_exe">{t key="common.delete"}</button>
</form>


{if $success }
	<p>{$success}</p>
	<button class="ajax-link" data-class="{$class}" data-function="reload">{t key="common.reload_browser"}</button>
{/if}

{if $fail }
	<p class="error">{$fail}</p>
{/if}


<table style="margin-bottom:10px;">
	<tr>
		<td>{t key="release.project_release_code"}</td>
		<td>{$info.project_release_code}</td>
	</tr>
	<tr>
		<td>{t key="release.datetime"}</td>
		<td>{$info.datetime}</td>
	</tr>
	<tr>
		<td>{t key="release.timezone"}</td>
		<td>{$info.timezone}</td>
	</tr>
	<tr>
		<td>{t key="release.memo"}</td>
		<td>{$info.memo}</td>
	</tr>
	<tr>
		<td>{t key="release.type"}</td>
		<td>{$info.type}</td>
	</tr>
</table>
{if $flg}

	<button class="ajax-link" data-class="{$class}" data-function="release_exe">{t key="release.release_button"}</button>
{else}
	<p class="error">{$message}</p>
{/if}







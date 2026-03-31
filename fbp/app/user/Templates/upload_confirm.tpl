
<div>
	<table>
		<tr>
			<td class="status"></td>
			<td>{t key="user.name"}</td>
			<td>{t key="user.email"}</td>
		</tr>
		{foreach $list as $row}
			<tr>
				{if count($row.errors) == 0}
					<td>OK</td>
				{else}
					<td>
						{foreach $row.errors as $e}
							<p class="error">{$e}</p>
						{/foreach}
					</td>
				{/if}
				<td>{$row.name}</td>
				<td>{$row.email}</td>
			</tr>
		{/foreach}

	</table>
	
</div>


<div>
	<p>{t key="user.csv.password_setup_after_import"}</p>
	{if $next_flg}
	<button class="ajax-link" data-class="{$class}" data-function="upload_csv_exe">{t key="common.add"}</button>
	{else}
		<p class="error">{t key="user.csv.fix_errors"}</p>
	{/if}
</div>

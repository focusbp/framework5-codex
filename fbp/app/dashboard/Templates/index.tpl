<div class="dashboard-grid">
	{if $dashboard_empty}
		<div class="dashboard-empty">{t key="dashboard.empty"}</div>
	{else}
		{foreach $dashboard_rows as $row}
			<div class="dashboard-row">
				{foreach $row as $item}
					<div class="dashboard-item dashboard-col-{$item.column_width}">
						{$item.html nofilter}
					</div>
				{/foreach}
			</div>
		{/foreach}
	{/if}
</div>

<style>
	.dashboard-grid {
		padding: 10px 5px 20px 5px;
	}
	.dashboard-row {
		display: grid;
		grid-template-columns: repeat(3, minmax(0, 1fr));
		gap: 14px;
		margin-bottom: 14px;
	}
	.dashboard-item {
		background: #ffffff;
		border: 1px solid #dadada;
		border-radius: 6px;
		padding: 12px;
		box-sizing: border-box;
		min-height: 120px;
	}
	.dashboard-col-1 {
		grid-column: span 1;
	}
	.dashboard-col-2 {
		grid-column: span 2;
	}
	.dashboard-col-3 {
		grid-column: span 3;
	}
	.dashboard-empty {
		padding: 20px;
		background: #f7f7f7;
		border: 1px solid #dedede;
		border-radius: 6px;
	}

	@media (max-width: 900px) {
		.dashboard-row {
			grid-template-columns: repeat(1, minmax(0, 1fr));
		}
		.dashboard-col-1,
		.dashboard-col-2,
		.dashboard-col-3 {
			grid-column: span 1;
		}
	}
</style>

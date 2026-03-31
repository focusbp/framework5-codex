<div id="setting_panel_tabs">
	<ul>
		<li><a href="#tabs-1" invoke-class="panel" invoke-function="release_backup">{t key="panel.release_backup.title"}</a></li>
	</ul>
	<div id="tabs-1" style="display: block;overflow: hidden;">

		<table class="release-table">
			{if !$MYSESSION.testserver}
				<tr>
					<td>
						<button class="ajax-link lang"
								data-class="release"
								data-function="release"
								style="float:inherit;margin-top:0px;">{t key="release.release_button"}</button>
					</td>
					<td>
						<span>{t key="panel.release_backup.release_help"}</span>
					</td>
				</tr>
			{/if}
			<tr>
				<td>
					<button class="ajax-link"
							data-class="restore"
							data-function="download_zip"
							style="float:inherit;margin-top:0px;">{t key="panel.release_backup.backup_button"}</button>
				</td>
				<td>
					<span>{t key="panel.release_backup.backup_help"}</span>
				</td>
			</tr>
			<tr>
				<td>
					<button class="ajax-link"
							data-class="restore"
							data-function="restore"
							style="float:inherit;margin-top:0px;">{t key="restore.restore_button"}</button>
				</td>
				<td>
					<span>{t key="panel.release_backup.restore_help"}</span>
				</td>
			</tr>
		</table>


	</div>
</div>

<script>
	$(function () {
		// jQuery UI Tabs 初期化
		$("#setting_panel_tabs").tabs({
		});
	});
</script>

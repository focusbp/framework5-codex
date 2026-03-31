

{if $flg}
	<p>{$message}</p>
	<form method="post" id="restore_upload_file">
		<div class="flex-container">
		<div class="flex-full">


			<input type="file" name="restore_file" data-text="{t key='common.file_upload'}" class="fr_image_paste">

		</div>

		</div>
		<button class="ajax-link" data-form="restore_upload_file" data-class="{$class}" data-function="restore_confirm">{t key="common.next"}</button>
	</form>
{else}
	<p class="error">{$message}</p>
{/if}







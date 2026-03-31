

{if $flg}
	<p>{$message}</p>
	<form method="post" id="release_upload_file">
		<div class="flex-container">
		<div class="flex-full">


			<input type="file" name="release_file" data-text="{t key='common.file_upload'}" class="fr_image_paste">

		</div>

		</div>
		<button class="ajax-link" data-form="release_upload_file" data-class="{$class}" data-function="release_confirm">{t key="release.release_button"}</button>
	</form>
{else}
	<p class="error">{$message}</p>
{/if}







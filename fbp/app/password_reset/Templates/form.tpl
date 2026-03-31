<form id="password_reset_form">
	<div>
		<p>{t key="password_reset.new_password"}</p>
		<input type="password" name="password" value="">
		<p class="error_message error_password"></p>
	</div>
	<div>
		<p>{t key="password_reset.confirm_password"}</p>
		<input type="password" name="password_confirm" value="">
		<p class="error_message error_password_confirm"></p>
	</div>
</form>

<button class="ajax-link" data-class="password_reset" data-function="reset_exe" data-form="password_reset_form">{t key="password_reset.reset_password_button"}</button>

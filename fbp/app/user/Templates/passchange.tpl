<form id="password_form">

	<p>{t key="password_reset.new_password"}</p>
	<input type="password" name="password" value="">
	<p class="error_message error_password"></p>

</form>

<button class="ajax-link" data-class="user" data-function="passchange_exe" data-form="password_form">{t key="common.save"}</button>

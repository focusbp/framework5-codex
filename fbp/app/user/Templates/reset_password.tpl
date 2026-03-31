<form id="user_password_reset_form">
	<input type="hidden" name="id" value="{$data.id}">

	<p>{t key="user.login_id"}</p>
	<p>{$data.login_id}</p>

	<p>{t key="user.email"}</p>
	<p>{$data.email}</p>

	<p>{t key="user.password_setup_user_help"}</p>

	<button class="ajax-link" data-class="user" data-function="password_reset_exe" data-form="user_password_reset_form">{t key="common.submit"}</button>
</form>

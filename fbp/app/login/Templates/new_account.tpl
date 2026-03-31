
<p>{t key="login.new_account_help"}</p>

<form id="new_form" style="height:300px;">
	<input type="hidden" name="class" value="login">
	<input type="hidden" name="function" value="make_new_account">
	<div class="form-wrap form-wrap-validation has-error">
		<p>{t key="user.login_id"}</p>
		<input type="text" name="login_id" value="{$login_id}" autocomplete="username">
		<p class="error_message error_login_id"></p>
		<p style="margin-top:10px;">{t key="login.password"}</p>
		<input type="password" name="password" value="{$password}" autocomplete="current-password">
		<p class="error_message error_password"></p>


	</div>	

	<button class="ajax-link" data-form="new_form" style="float:right;margin-top:18px;">{t key="login.make_new_account_button"}</button>

</form>

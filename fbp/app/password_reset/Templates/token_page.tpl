<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="user-scalable=1">
		{include file="{$base_template_dir}/publicsite_header.tpl"}
		<title>{t key="password_reset.set_password_page_title"} : Focus Business Platform</title>
	</head>
	<body>
		<article class="class_style_{$class}">
			<div id="login_area">
				<h3>{t key="password_reset.set_password_title"}</h3>
				<p>{t key="password_reset.set_password_help"}</p>

				<form method="post" action="app.php">
					<input type="hidden" name="class" value="password_reset">
					<input type="hidden" name="function" value="token_reset_exe">
					<input type="hidden" name="token" value="{$token}">

					<p>{t key="user.login_id"}</p>
					<input type="text" value="{$data.login_id}" disabled>

					<p style="margin-top:10px;">{t key="password_reset.new_password"}</p>
					<input type="password" name="password" value="">
					<p class="error">{$err_password}</p>

					<p style="margin-top:10px;">{t key="password_reset.confirm_password"}</p>
					<input type="password" name="password_confirm" value="">
					<p class="error">{$err_password_confirm}</p>

					<p class="error">{$err_token}</p>

					<button class="loginbutton" style="float:right;margin-top:18px;">{t key="common.update"}</button>
				</form>
			</div>
		</article>
		{include file="{$base_template_dir}/publicsite_footer.tpl"}
	</body>
</html>

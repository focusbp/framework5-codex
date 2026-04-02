<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="user-scalable=1">
		{include file="{$base_template_dir}/publicsite_header.tpl"}
		<link rel="icon" href="app.php?class=base&function=img&file=favicon.ico" type="image/x-icon" id="favicon">
		<title>{t key="login.page_title"} : Focus Business Platform</title>
	</head>
	<body>
		<article class="class_style_{$class}">


			<div id="login_area">

				<div id="form" style="margin-bottom:100px;"></div>	

				<div style="clear:both;"></div>

			</div>


		</article>
		{include file="{$base_template_dir}/publicsite_footer.tpl"}
	</body>

	<script>
		$(function () {
			var fd = new FormData();
			fd.append("class", "login");
			fd.append("function", "login_form");
			appcon("app.php", fd);
		});
	</script>

</html>

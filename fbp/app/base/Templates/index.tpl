<!DOCTYPE html>
<html>
	{include file="{$base_template_dir}/header.tpl"}
	<body>
		{include file="topbar.tpl"}
		<article>

			<div id="menu_area" class="lang_check_area" data-classname="base">

				<div id="menu"></div>

			</div>

			<div class="content">


				<div id="work_area">

				

				</div>
					
				<div id="work_area_second">

				</div>
					
			</div>

		</article>

		<footer>
			<div id="appcode" style="display: none;">{$appcode}</div>
			{if $testserver}
				<button id="show_debug">{$base_i18n.debug}</button>
			{/if}
			<div class="copyright">{$base_i18n.app_name}</div>
		</footer>


		{include file="{$base_template_dir}/footer.tpl"}


	</body>

</html>

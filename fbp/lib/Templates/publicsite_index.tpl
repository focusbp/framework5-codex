<!DOCTYPE html>
<html>
<head>
{include file="{$base_template_dir}/publicsite_header.tpl"}
{$html_header}
</head>

<body class="publicsite-body">
    <article class="class_style_{$class} lang_check_area publicsite-shell" data-classname="{$class}">
		<div class="publicsite-site-header"></div>

		<section class="public_main_section">
			<div class="publicsite-main-inner">
				<div id="multi_dialog_{$class}" class="getting_dialog_id publicsite-content">
{$contents nofilter}
				</div>
			</div>
		</section>

		<div class="publicsite-site-footer"></div>
			
	</article>
	
	{include file="{$base_template_dir}/publicsite_footer.tpl"}
</body>
</html>

<form id="dbs_db_fields_add_form">

	<input type="hidden" name="db_id" value="{$post.db_id}">
	<div>
		<p class="lang">{t key="db.field_title"}:</p>
		<input type="text" name="parameter_title" value="{$post.parameter_title}">

		<p class="error_message lang error_parameter_title"></p>
	</div>	
	<div>
		<p class="lang">{t key="db.field_name"}:</p>
		<input type="text" name="parameter_name" value="{$post.parameter_name}">

		<p class="error_message lang error_parameter_name"></p>
	</div>
	<div>
		<p class="lang">{t key="db.field_description"}:</p>
		<input type="text" name="parameter_description" value="{$post.parameter_description}">
		<p class="error_message lang error_parameter_description"></p>
	</div>
		
	<div>
		<p class="lang">{t key="db.field_description_bot"}:</p>
		<input type="text" name="parameter_description_bot" value="{$post.parameter_description_bot}">
		<p class="error_message lang error_parameter_description_bot"></p>
	</div>

	<div>
		<p class="lang">{t key="db.field_type"}:</p>
		{html_options name="type" id="type_event" selected=$post.type options=$type_opt}
	</div>

	<div id="area_option">
		{include file="_area_option.tpl"}
	</div>

		
	<div class="image_width">
		<p class="lang">{t key="db.image_width"}:</p>
		<input class="image_width" type="text" name="image_width" value="{$post.image_width}">
	</div>
	
	<div class="image_width">
		<p class="lang">{t key="db.thumbnail_width"}:</p>
		<input type="text" name="image_width_thumbnail" value="{$post.image_width_thumbnail}">
	</div>

	<div>
		<p class="lang">{t key="db.validation_label"}:</p>
		{html_options name="validation" selected=$post.validation options=$validation_opt}
	</div>
	<div>
		<p class="lang">{t key="db.duplicate_check"}:</p>
		{html_options name="duplicate_check" selected=$post.duplicate_check options=$duplicate_check_opt}
	</div>
	<div>
		<p class="lang">{t key="db.format_check"}:</p>
		{html_options name="format_check" selected=$post.format_check options=$format_check_title_opt}
	</div>
	<div>
		<p class="lang">{t key="db.default"}:</p>
		<input type="text" name="default_value" value="{$post.default_value}">
	</div>
	
	<div>
		<p class="lang">{t key="db.title_color"}:</p>
		<input type="text" name="title_color" value="{$post.title_color}" class="colorpicker">
	</div>


	<div>
		<button class="ajax-link lang" data-form="dbs_db_fields_add_form" data-class="{$class}" data-function="add_fields_exe">{t key="common.add"}</button>
	</div>

</form>

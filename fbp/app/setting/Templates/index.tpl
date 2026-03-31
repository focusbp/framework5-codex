
<form id="setting_form">

	<h5 style="margin-top:20px;">{t key="setting.language"}</h5>
	<table>
		<tr>
			<td>{t key="setting.framework_language_code"}</td>
			<td>{html_options name="framework_language_code" options=$arr_framework_language_code selected=$setting.framework_language_code}</td>
		</tr>
	</table>
	
	<h5 style="margin-top:20px;">{t key="setting.releasing"}</h5>
	<table>
		<tr>
			<td>{t key="setting.project_release_code"}</td>
			<td><input type="text" name="project_release_code" value="{$setting.project_release_code}"></td>
		</tr>
	</table>
		
	<h5 style="margin-top:20px;">{t key="setting.ssl_section"}</h5>
	<table>
		<tr>
			<td>{t key="setting.ssl"}</td>
			<td>{html_options name="ssl" options=$arr_ssl selected=$setting.ssl}</td>
		</tr>
	</table>
		
	<h5 style="margin-top:20px;">{t key="setting.timezone_section"}</h5>
	<table>
		<tr>
			<td>{t key="setting.timezone"}</td>
			<td>
		{html_options name="timezone" options=$timezones selected=$setting.timezone}
			</td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">{t key="setting.mode"}</h5>
	<table>
		<tr>
			<td>{html_options name="force_testmode" options=$arr_force_testmode selected=$setting.force_testmode}</td>
		</tr>
	</table>
		
	<h5 style="margin-top:20px;">{t key="setting.developer_panel"}</h5>
	<table>
		<tr>
			<td>{html_options name="show_developer_panel" options=$arr_show_developer_panel selected=$setting.show_developer_panel}</td>
		</tr>
	</table>
		
	<h5 style="margin-top:20px;">{t key="setting.source_code_directory"}</h5>
	<p>{t key="setting.source_code_directory_help"}</p>
	<table>
	  <tr>
		<td>
		  <input type="text" name="source_code_dir" value="{$setting.source_code_dir}">
		</td>
	  </tr>
	</table>
		
	<h5 style="margin-top:20px;">{t key="setting.show_homepage_link"}</h5>
	<table>
		<tr>
			<td>{html_options name="show_menu_homepage" options=$arr_show_menu selected=$setting.show_menu_homepage}</td>
		</tr>
	</table>


	<h5 style="margin-top:20px;">{t key="setting.rewrite_for_root_access"}</h5>
	<table>
		<tr>
			<td>{t key="setting.class_name_default_login"}<input type="text" name="rewrite_rule_root" value="{$setting.rewrite_rule_root}" style="width:200px;"></td>
			<td>{t key="setting.function"}<input type="text" name="rewrite_rule_function" value="{$setting.rewrite_rule_function}" style="width:200px;"></td>
		</tr>
	</table>
		
	<h5 style="margin-top:20px;">{t key="setting.default_class_name_in_url"}</h5>
	<table>
		<tr>
			<td>{t key="setting.default_class"}<input type="text" name="default_class_name" value="{$setting.default_class_name}" style="width:200px;"></td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">{t key="setting.startup_class"}</h5>
	<p>{t key="setting.startup_class_help"}</p>
	<table>
		<tr>
			<td>{t key="setting.class"}<input type="text" name="startup_class1" value="{$setting.startup_class1}" style="width:200px;"></td>
			<td>{t key="setting.function"}<input type="text" name="startup_function1" value="{$setting.startup_function1}" style="width:200px;"></td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">{t key="setting.mail_server_setting"}</h5>
	<table>
		<tr>
			<td>{t key="setting.mail_address_from"}</td>
			<td><input type="text" name="smtp_from" value="{$setting.smtp_from}"></td>
		</tr>
		<tr>
			<td>{t key="setting.mail_server"}</td>
			<td><input type="text" name="smtp_server" value="{$setting.smtp_server}"></td>
		</tr>
		<tr>
			<td>{t key="setting.mail_port"}</td>
			<td><input type="text" name="smtp_port" value="{$setting.smtp_port}"></td>
		</tr>
		<tr>
			<td>{t key="setting.mail_user"}</td>
			<td><input type="text" name="smtp_user" value="{$setting.smtp_user}"></td>
		</tr>
		<tr>
			<td>{t key="setting.mail_password"}</td>
			<td><input type="password" name="smtp_password" value="" placeholder="{$masked_setting.smtp_password}"></td>
		</tr>
		<tr>
			<td>SMTPSecure</td>
			<td>{html_options name="smtp_secure" options=$arr_smtp_secure selected=$setting.smtp_secure}</td>
		</tr>
		<tr>
			<td>{t key="setting.email_for_testing"}</td>
			<td><input type="text" name="smtp_email_test" value="{$setting.smtp_email_test}"></td>
		</tr>

	</table>
	<button class="ajax-link lang" data-class="setting" data-function="update" data-form="setting_form" data-send_test_mail="1">{t key="setting.submit_and_send_test_mail"}</button>

	<h5 style="margin-top:20px;">{t key="setting.vimeo_setting"}</h5>
	<table>
		<tr>
			<td>{t key="setting.access_token"}</td>
			<td><input type="password" name="vimeo_access_token" value="" placeholder="{$masked_setting.vimeo_access_token}"></td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">{t key="setting.square_setting"}</h5>
	<table>
		<tr>
			<td>{t key="setting.application_id"}</td>
			<td><input type="text" name="square_application_id" value="{$setting.square_application_id}"></td>
		</tr>
		<tr>
			<td>{t key="setting.access_token"}</td>
			<td><input type="password" name="square_access_token" value="" placeholder="{$masked_setting.square_access_token}"></td>
		</tr>
		<tr>
			<td>{t key="setting.location_id"}</td>
			<td><input type="text" name="square_location_id" value="{$setting.square_location_id}"></td>
		</tr>
		<tr>
			<td>{t key="setting.currency"}</td>
			<td>{html_options name="currency" options=$currency_list selected=$setting.currency}</td>
		</tr>
	</table>
	<p class="ajax-link lang" data-class="setting" data-function="square" style="color:blue;text-decoration: underline;">{t key="setting.square_test"}</p>


	<h5 style="margin-top:20px;">{t key="setting.google_settings"}</h5>
	<table>
		<tr>
			<td>{t key="setting.api_key"}</td>
			<td><input type="password" name="api_key_map" value="" placeholder="{$masked_setting.api_key_map}"></td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">{t key="setting.api_authentication_hmac"}</h5>
	<table>
		<tr>
			<td>{t key="setting.api_key"}</td>
			<td><input type="password" name="api_key" value="" placeholder="{$masked_setting.api_key}" readonly></td>
		</tr>
		<tr>
			<td>{t key="setting.api_secret"}</td>
			<td><input type="password" name="api_secret" value="" placeholder="{$masked_setting.api_secret}" readonly></td>
		</tr>
	</table>
		
	<h5 style="margin-top:20px;">{t key="setting.line_bot_settings"}</h5>
	<table>
		<tr>
			<td>{t key="setting.webhook_url"}</td>
			<td><input type="text" value="{$line_webhook_url}" readonly></td>
		</tr>
		<tr>
			<td>{t key="setting.channel_secret"}</td>
			<td><input type="password" name="line_channel_secret" value="" placeholder="{$masked_setting.line_channel_secret}"></td>
		</tr>
		<tr>
			<td>{t key="setting.channel_access_token"}</td>
			<td><input type="password" name="line_accesstoken" value="" placeholder="{$masked_setting.line_accesstoken}"></td>
		</tr>
		<tr>
			<td>{t key="setting.log_file_path"}</td>
			<td><input type="text" name="line_logfile" value="{$setting.line_logfile}"></td>
		</tr>
		<tr>
			<td>{t key="setting.bot_greeting_message"}</td>
			<td><textarea name="line_bot_greeting_message">{$setting.line_bot_greeting_message}</textarea></td>
		</tr>
	</table>
		

	<h5 style="margin-top:20px;">{t key="setting.openai_settings"}</h5>
	
	<p>{t key="setting.for_bot"}</p>
	<table>
		<tr>
			<td>{t key="setting.api_key"}</td>
			<td><input type="password" name="chatgpt_api_key" value="" placeholder="{$masked_setting.chatgpt_api_key}"></td>
		</tr>
		<tr>
			<td>{t key="setting.endpoint_url"}<br /><span style="font-size:10px;">{t key="setting.for_completions"}</span></td>
			<td><input type="text" name="chatgpt_api_url" value="{$setting.chatgpt_api_url}"></td>
		</tr>
		<tr>
			<td>{t key="setting.default_model"}</td>
			<td><input type="text" name="chatgpt_api_model" value="{$setting.chatgpt_api_model}"></td>
		</tr>
	</table>
		
	<p>{t key="setting.for_coding"}</p>
	<table>
		<tr>
			<td>{t key="setting.api_key"}</td>
			<td><input type="password" name="chatgpt_coding_key" value="" placeholder="{$masked_setting.chatgpt_coding_key}"></td>
		</tr>
		<tr>
			<td>{t key="setting.endpoint_url"}<br /><span style="font-size:10px;">{t key="setting.for_completions"}</span></td>
			<td><input type="text" name="chatgpt_coding_url" value="{$setting.chatgpt_coding_url}"></td>
		</tr>
		<tr>
			<td>{t key="setting.default_model"}</td>
			<td><input type="text" name="chatgpt_coding_model" value="{$setting.chatgpt_coding_model}"></td>
		</tr>
	</table>
	
	<p>{t key="setting.common"}</p>
	<table>
		<tr>
			<td>{t key="setting.log_file"}</td>
			<td><input type="text" name="openai_logfile" value="{$setting.openai_logfile}"></td>
		</tr>
		<tr>
			<td>{t key="setting.max_vector_store_files"}</td>
			<td><input type="text" name="max_vs" value="{$setting.max_vs}"></td>
		</tr>
	</table>
		
	<p>{t key="setting.endpoints_for_completions"}</p>
	<table>
		<tr>
			<td>OpenAI</td>
			<td>https://api.openai.com/v1/chat/completions</td>
		</tr>
		<tr>
			<td>Sakura</td>
			<td>https://api.ai.sakura.ad.jp/v1/chat/completions</td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">{t key="setting.encrypt_decrypt"}</h5>
	<table>
		<tr>
			<td>{t key="setting.secret"}</td>
			<td><input type="text" name="secret" value="{$setting.secret}"></td>
		</tr>
		<tr>
			<td>IV</td>
			<td><input type="text" name="iv" value="{$setting.iv}"></td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">{t key="setting.robots_txt"}</h5>
	<table>
		<tr>
			<td>robots.txt</td>
			<td><textarea name="robots">{$setting.robots}</textarea></td>
		</tr>
	</table>
		
	<h5 style="margin-top:20px;">{t key="setting.viewport"}</h5>
	<table>
		<tr>
			<td>{t key="setting.management_side"}</td>
			<td><input type="text" name="viewport_base" value="{$setting.viewport_base}"></td>
		</tr>
		<tr>
			<td>{t key="setting.public_side"}</td>
			<td><input type="text" name="viewport_public" value="{$setting.viewport_public}"></td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">{t key="setting.system_name_section"}</h5>
	<table>
		<tr>
			<td>{t key="setting.system_name"} {literal}{$setting.system_name}{/literal}</td>
			<td><input type="text" name="system_name" value="{$setting.system_name}"></td>
		</tr>
		<tr>
			<td>{t key="setting.system_tag_line"}</td>
			<td><input type="text" name="system_tag_line" value="{$setting.system_tag_line}"></td>
		</tr>
		<tr>
			<td>{t key="setting.login_logo"}</td>
			<td><input type="file" name="login_logo" class="fr_image_paste" data-text="Image Uploader">
				<br /><button class="ajax-link" data-class="{$class}" data-function="delete_login_logo" style="margin-top:0px;">{t key="setting.delete_login_logo"}</button>
			</td>
		</tr>
		<tr>
			<td>favicon.ico</td>
			<td><input type="file" name="favicon" class="fr_image_paste" data-text="Image Uploader">
				<br /><button class="ajax-link" data-class="{$class}" data-function="delete_favicon" style="margin-top:0px;">{t key="setting.delete_favicon"}</button>
			</td>
		</tr>
	</table>

	<h5 style="margin-top:20px;">{t key="setting.website"}</h5>
	<table>
		<tr>
			<td>{t key="setting.website_url"}</td>
			<td><input type="text" name="website_url" value="{$setting.website_url}"></td>
		</tr>
	</table>

</form>
		
		
<button class="ajax-link lang" data-class="setting" data-function="update" data-form="setting_form">{t key="setting.submit"}</button>

<div style="clear:both;margin-bottom:30px;"></div>

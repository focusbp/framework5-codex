<?php

/*
 *  YOU CAN'T CHANGE THIS PROJECT.
 *  It will be overwritten when the framework updates.
 */

class setting {

	private $ffm;
	private $sensitive_keys = [
		"api_key",
		"api_secret",
		"api_key_map",
		"chatgpt_api_key",
		"chatgpt_coding_key",
		"line_accesstoken",
		"line_channel_secret",
		"smtp_password",
		"square_access_token",
		"vimeo_access_token",
		"vimeo_client_secret",
	];
	private $arr_display_errors = [0 => "On Console", 1 => "Display to Window"];
	private $arr_smtp_secure = [0 => "false", 1 => "tls", 2 => "ssl"];
	private $arr_force_testmode = ["0" => "Production Mode", "1" => "Developer mode"];
	private $arr_show_menu = [0=>"No",1=>"Yes"];
	private $arr_ssl = [0=>"http and https","https only"];
	private $arr_flg_show_lang_on_chat = [0=>"Show",1=>"Hide"];
	private $arr_show_developer_panel = [0=>"Hide",1=>"Show"];
	private $arr_framework_language_code = [];
	private $currency_list = [];

	function __construct(Controller $ctl) {
		$this->ffm = $ctl->db("setting");
		$ctl->assign("arr_customize", [0 => "Default", 1 => "Customize"]);
		$ctl->assign("arr_onoff", [0 => "On", 1 => "Off"]);
		$ctl->assign("arr_display_errors", $this->arr_display_errors);
		$ctl->assign("arr_smtp_secure", $this->arr_smtp_secure);
		$this->arr_framework_language_code = I18nSimple::get_language_options();
		$ctl->assign("arr_framework_language_code", $this->arr_framework_language_code);
		$ctl->assign("arr_force_testmode", $this->arr_force_testmode);
		$ctl->assign("arr_show_menu", $this->arr_show_menu);
		$ctl->assign("arr_ssl",$this->arr_ssl);
		$ctl->assign("arr_flg_show_lang_on_chat",$this->arr_flg_show_lang_on_chat);
		$ctl->assign("arr_show_developer_panel",$this->arr_show_developer_panel);
		$ctl->assign("arr_line_forward_unknown_to_manager", $this->get_line_forward_unknown_to_manager_options($ctl));
		
		$this->currency_list = include (__DIR__."/currency.php");
		$ctl->assign("currency_list", $this->currency_list);
		
		$timezones = array_combine(timezone_identifiers_list(), timezone_identifiers_list());
		$ctl->assign('timezones', $timezones);

	}

	function update(Controller $ctl) {
		$setting = $this->ffm->get(1);
		if ($setting == null) {
			$setting = array();
			$this->ffm->insert($setting);
		}
		foreach ($ctl->POST() as $key => $val) {
			if (in_array($key, $this->sensitive_keys, true) && trim((string) $val) === "") {
				continue;
			}
			$setting[$key] = $val;
		}
		if (empty($setting["rewrite_rule_root"])) {
			$setting["rewrite_rule_root"] = "login";
		}
		if (empty($setting["rewrite_rule_function"])) {
			$setting["rewrite_rule_function"] = "page";
		}
		if (empty($setting["currency"])) {
			$setting["currency"] = "JPY";
		}
		if (empty($setting["robots"])) {
			$setting["robots"] = "User-Agent: *\nAllow: /\n";
		}
		if (empty($setting["timezone"])){
			$setting["timezone"] = 'Asia/Tokyo';
		}
		$setting["framework_language_code"] = $this->normalize_framework_language_code((string) ($setting["framework_language_code"] ?? "en"));
		$setting["lang_priority"] = 1;
		$setting["lang_default"] = I18nSimple::get_legacy_lang_code_from_setting($setting);
		if (!isset($setting["flg_show_lang_on_chat"])) {
			$setting["flg_show_lang_on_chat"] = 0;
		}
		if (!isset($setting["line_forward_unknown_to_manager"])) {
			$setting["line_forward_unknown_to_manager"] = 0;
		}
		
		
		$this->ffm->update($setting);
		$ctl->set_session("setting", $setting);

		// Replace .htaccess
		$path_server = $_SERVER['REQUEST_URI'];
		$directoryPath = pathinfo($path_server, PATHINFO_DIRNAME);
		if(endsWith($directoryPath, "/fbp")){
			$directoryPath = substr($directoryPath,0, strlen($directoryPath)-4);
		}
		if($directoryPath == "/"){
			$directoryPath = "";
		}
		$template = file_get_contents(dirname(__FILE__) . "/Templates/htaccess.tpl");
		$template = str_replace('{$class}', $setting["rewrite_rule_root"], $template);
		$template = str_replace('{$function}', $setting["rewrite_rule_function"], $template);
		$template = str_replace('{$subpath}',$directoryPath,$template);
		$template = str_replace('{$default_class_name}',$setting["default_class_name"],$template);
		if($setting["ssl"] == 1){
			$template = str_replace('{$ssl}','RewriteCond %{HTTPS} off' . "\n" . 'RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R]',$template);
		}else{
			$template = str_replace('{$ssl}',"",$template);	
		}
		file_put_contents(dirname(__FILE__) . "/../../../.htaccess", $template);

		// Replace robots.txt
		file_put_contents(dirname(__FILE__) . "/../../../robots.txt", $setting["robots"]);

		// Login Logo
		if ($ctl->is_posted_file("login_logo")) {
			$ctl->save_posted_file("login_logo", "login_logo");
		}

		// 
		// favicon
		if ($ctl->is_posted_file("favicon")) {
			$ctl->save_posted_file("favicon", "favicon");
		}
		
		// Test Mail
		if ($ctl->POST("send_test_mail") == 1) {
			$setting = $this->ffm->get(1);
			$ctl->set_session("setting", $setting);
			$to = $setting["smtp_email_test"];
			$ctl->send_mail_string($setting["smtp_from"], $to, "TEST", "This is test mail from setting.\n" . $_SERVER["HTTP_HOST"]);
		}

		$ctl->res_reload();
	}

	function page(Controller $ctl) {

		$ctl->generate_api_credentials();
		$setting = $this->ffm->get(1);

		if (empty($setting["user_type_name0"])) {
			$setting["user_type_name0"] = "User";
		}
		if (empty($setting["currency"])) {
			$setting["currency"] = "JPY";
		}
		if (empty($setting["timezone"])){
			$setting["timezone"] = 'Asia/Tokyo';
		}
		$setting["framework_language_code"] = $this->normalize_framework_language_code((string) ($setting["framework_language_code"] ?? "en"));
		$setting["lang_priority"] = 1;
		$setting["lang_default"] = I18nSimple::get_legacy_lang_code_from_setting($setting);
		if (!isset($setting["flg_show_lang_on_chat"])) {
			$setting["flg_show_lang_on_chat"] = 0;
		}
		if (!isset($setting["line_forward_unknown_to_manager"])) {
			$setting["line_forward_unknown_to_manager"] = 0;
		}

		$ctl->assign("setting", $setting);
		$ctl->assign("masked_setting", $this->mask_sensitive_setting($setting));
		$ctl->assign("line_webhook_url", $ctl->get_APP_URL("webhook_line", "receive"));

		$ctl->show_multi_dialog("setting", "index.tpl", $ctl->t("setting.dialog.index"), 800, "_edit_button.tpl");
	}

	function json_upload(Controller $ctl) {
		$ctl->deny_forbidden_access();
	}

	function json_upload_exe(Controller $ctl) {
		$ctl->deny_forbidden_access();
	}

	function json_download(Controller $ctl) {
		$ctl->deny_forbidden_access();
	}

	function delete_login_logo(Controller $ctl) {
		$ctl->delete_saved_file("login_logo");
		$ctl->show_notification_text($ctl->t("setting.notification.login_logo_deleted"));
	}
	
	function delete_favicon(Controller $ctl) {
		$ctl->delete_saved_file("favicon");
		$ctl->show_notification_text($ctl->t("setting.notification.favicon_deleted"));
	}

	function square(Controller $ctl) {

		// Get customer informations before input credit card.
		$name = "Test";
		$mail = "info@soshiki-kaikaku.com";
		$address = "テスト住所";
		$amount = 100; // 100 Yen

		$callback_parameter_array = ["name" => $name, "mail" => $mail, "address" => $address, "amount" => $amount];

		// Show credit card dialog.
		$ctl->show_square_dialog("setting", "pay", $callback_parameter_array);
	}

	function pay(Controller $ctl) {

		// You can call set_square($square_application_id=,$square_access_token)  here to change square account.
		// $ctl->set_square("","");
		// Get parameters from the framework.
		$param = $ctl->get_square_callback_parameter_array();

		try {
			// Regist Customer SQUARE and get customer id
			$square_customer_id = $ctl->square_regist_customer($param["name"], $param["mail"], $param["address"]);

			// Regist the Card
			$card_id = $ctl->square_regist_card($square_customer_id);

			// ------------------------------------------------------------------------------
			// If you save square_customer_id and card_id, You can execute payment any time , any amount!
			// ------------------------------------------------------------------------------
			// Execute Payment
			$result = $ctl->square_payment($square_customer_id, $card_id, $param["amount"]);

			if ($result) {
				$ctl->close_square_dialog();
				$ctl->assign("msg", "SUCCESS");
				$ctl->show_multi_dialog("square_dialog", "square_result.tpl", $ctl->t("setting.square_result"));
			} else {
				$ctl->close_square_dialog();
				$ctl->assign("msg", "FAIL");
				$ctl->show_multi_dialog("square_dialog", "square_result.tpl", $ctl->t("setting.square_result"));
			}
		} catch (Exception $e) {
			$ctl->show_square_dialog("square_sample", "pay", $param, $e->getMessage());
		}
	}

	private function mask_sensitive_setting($setting) {
		if (!is_array($setting)) {
			return [];
		}
		foreach ($this->sensitive_keys as $key) {
			if (isset($setting[$key])) {
				$setting[$key] = $this->mask_secret_value((string) $setting[$key]);
			}
		}
		return $setting;
	}

	private function mask_secret_value(string $value): string {
		if ($value === "") {
			return "";
		}
		return "Configured";
	}

	private function normalize_framework_language_code(string $code): string {
		$code = strtolower(trim($code));
		if (!preg_match('/^[a-z]{2}$/', $code)) {
			return "en";
		}
		return $code;
	}

	private function get_line_forward_unknown_to_manager_options(Controller $ctl): array {
		return [
			0 => $ctl->t("setting.line_forward_unknown_to_manager.option.forward"),
			1 => $ctl->t("setting.line_forward_unknown_to_manager.option.no_forward"),
		];
	}

}

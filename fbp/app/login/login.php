<?php

class login {

	private $ffm_user;
	private $remember_me_cookie_name = "remember_me";
	private $remember_me_ttl = 2592000; // 30 days

	function __construct(Controller $ctl) {
		$ctl->set_check_login(false);
		$this->ffm_user = $ctl->db("user", "user");
	}

	function page(Controller $ctl) {		
		//ログイン画面を表示

		$ctl->display("login.tpl");
	}
	
	function make_new_account(Controller $ctl){
		
		$post = $ctl->POST();
		
		$login_id = $post["login_id"];
		$password = $post["password"];
		
		// 半角英数字のバリデーション
		if (!preg_match('/^[a-zA-Z0-9@._\-!#$%&*?]+$/', $login_id)) {
		    $ctl->res_error_message("login_id", $ctl->t("login.validation.login_id_format"));
		}

		if (!preg_match('/^[a-zA-Z0-9@._\-!#$%&*?]+$/', $password)) {
		    $ctl->res_error_message("password", $ctl->t("login.validation.password_format"));
		}
		
		if($ctl->count_res_error_message()>0){
			return;
		}
		
		$user = array();
		$user["login_id"] = $login_id;
		$user["password"] = $this->hash_password($password);
		$user["name"] = "Admin";
		$user["type"] = 0;
		$user["email"] = "";
		$this->ffm_user->insert($user);
		
		// Make .htaccess
		$path_server = $_SERVER['REQUEST_URI'];
		$directoryPath = pathinfo($path_server, PATHINFO_DIRNAME);
		if(endsWith($directoryPath, "/fbp")){
			$directoryPath = substr($directoryPath,0, strlen($directoryPath)-4);
		}
		if($directoryPath == "/"){
			$directoryPath = "";
		}
		$template = file_get_contents(dirname(__FILE__) . "/../setting/Templates/htaccess.tpl");
		$template = str_replace('{$class}', "login", $template);
		$template = str_replace('{$function}', "page", $template);
		$template = str_replace('{$subpath}',$directoryPath,$template);
		$template = str_replace('{$default_class_name}',"",$template);
		$template = str_replace('{$ssl}',"",$template);	
		file_put_contents(dirname(__FILE__) . "/../../../.htaccess", $template);
	
		$ctl->show_multi_dialog("new_account", "finish_new_account.tpl", $ctl->t("login.dialog.make_new_account"));
	}
	
	function close(Controller $ctl){
		$ctl->close_all_dialog();
	}

	function login_form(Controller $ctl) {

		//ユーザが登録されているか確認
		$list = $this->ffm_user->select("type",0);
		if(count($list) == 0){
			$ctl->show_multi_dialog("new_account", "new_account.tpl", $ctl->t("login.dialog.make_new_account"));
			
		}else{
			$ctl->assign("user", null);
		}
		
		// Cookie
		if(!empty($_COOKIE["login_id"])){
			$cookie_login_id = $_COOKIE["login_id"];
		}else{
			$cookie_login_id = "";
		}
		if(!empty($_COOKIE[$this->remember_me_cookie_name])){
			$cookie_remember_me = $_COOKIE[$this->remember_me_cookie_name];
		}else{
			$cookie_remember_me = "";
		}
		if(!empty($_COOKIE["login_status"])){
			$cookie_login_status = $_COOKIE["login_status"];
		}else{
			$cookie_login_status = "";
		}

		// Logo check
		if ($ctl->is_saved_file("login_logo")) {
			$ctl->assign("flg_login_logo", true);
		}else{
			$ctl->assign("flg_login_logo", false);
		}

		// -------------
		// Cookieがログイン情報を持っている場合、チェックしてログイン処理
		// 持っていない場合は、ログインフォームを表示
		// -------------
		$login_id = $ctl->decrypt($cookie_login_id);
		$password = "";
		$user = null;

		if ($cookie_login_status == "logined" && $cookie_remember_me !== "") {
			$remember_login_id = $this->decode_remember_me_cookie($ctl, $cookie_remember_me);
			if ($remember_login_id !== "") {
				$user = $this->find_user_by_login_id($remember_login_id);
				if (is_array($user)) {
					$login_id = $remember_login_id;
					$password = "";
				}
			}
		}
		if (is_array($user)) {
			$this->login_ok($ctl, $user, $login_id, $password);
		} else {
			// デフォルト表示
			$ctl->assign("login_id", $login_id);
			$ctl->assign("password", "");
			
			$ctl->reload_area("#form", $ctl->fetch("form.tpl"));
		}
	}

	function check(Controller $ctl) {
		$login_id = $ctl->POST("login_id");
		$password = $ctl->POST("password");

		$user = $this->find_user_by_credentials($login_id, $password);
		if (is_array($user)) {
			$this->login_ok($ctl, $user, $login_id, $password);
		} else {
			$ctl->assign("login_id", $login_id);
			$ctl->assign("err_password", $ctl->t("validation.login.failed"));
			
			if($ctl->POST("_call_from") == "appcon"){
				$ctl->reload_area("#form", $ctl->fetch("form.tpl"));
			}else{
				$ctl->display("login.tpl");
			}
		}
	}

	function login_ok(Controller $ctl, $user, $login_id, $password) {
		
		$ctl->set_session("login", true);

		foreach ($user as $key => $val) {
			if ($key == "id") {
				$ctl->set_session("user_id", $user["id"]);
			} else {
				$ctl->set_session($key, $val);
			}
		}

		//admin判定
		if ($user["type"] == 0) {
			$ctl->set_session("app_admin", true);
		}

		//---------------
		// Cookie処理
		//---------------
		app_setcookie("login_id", $ctl->encrypt($login_id), strtotime('+30 days'));
		app_setcookie("login_status", "logined", strtotime('+30 days'));
		$this->set_remember_me_cookie($ctl, $login_id);
		
		if ((int) ($user["flg_password_change_required"] ?? 0) === 1) {
			$ctl->res_redirect("app.php?class=password_reset&function=force_page");
			return;
		}
		
		$ctl->res_redirect("app.php?class=base");
	}

	function logo(Controller $ctl) {
		$ctl->res_saved_image("login_logo");
	}
	
	function logo_default(Controller $ctl) {
		$ctl->res_image("images","login_logo.png");
	}

	function logout(Controller $ctl) {
		$windowcode = $ctl->get_windowcode();
		$_SESSION[$windowcode] = [];
		app_setcookie("login_id", "",time() - 3600);
		app_setcookie("password", "",time() - 3600);
		app_setcookie($this->remember_me_cookie_name, "",time() - 3600);
		app_setcookie("login_status", "",time() - 3600);
		$ctl->res_redirect("app.php?class=login");
	}

	private function find_user_by_credentials($login_id, $password) {
		$user_list = $this->ffm_user->select(["login_id", "status"], [$login_id, 0], true);
		if (count($user_list) !== 1) {
			return null;
		}

		$user = $user_list[0];
		$stored_password = (string) ($user["password"] ?? "");
		if ($stored_password === "" || $password === "") {
			return null;
		}

		$info = password_get_info($stored_password);
		if ((int) ($info["algo"] ?? 0) !== 0) {
			if (password_verify($password, $stored_password)) {
				return $user;
			}
			return null;
		}

		if (!hash_equals($stored_password, (string) $password)) {
			return null;
		}

		$user["password"] = $this->hash_password((string) $password);
		$this->ffm_user->update($user);
		return $user;
	}

	private function find_user_by_login_id($login_id) {
		$user_list = $this->ffm_user->select(["login_id", "status"], [$login_id, 0], true);
		if (count($user_list) !== 1) {
			return null;
		}
		return $user_list[0];
	}

	private function hash_password($password) {
		$hash = password_hash((string) $password, PASSWORD_DEFAULT);
		if (!is_string($hash) || $hash === "") {
			throw new Exception("Failed to hash password.");
		}
		return $hash;
	}

	private function set_remember_me_cookie(Controller $ctl, $login_id) {
		$exp = time() + (int) $this->remember_me_ttl;
		$ua = (string) ($_SERVER["HTTP_USER_AGENT"] ?? "");
		$payload = json_encode([
			"login_id" => (string) $login_id,
			"exp" => $exp,
			"ua" => hash("sha256", $ua),
		], JSON_UNESCAPED_SLASHES);
		if (!is_string($payload) || $payload === "") {
			return;
		}
		$token = $ctl->encrypt($payload);
		app_setcookie($this->remember_me_cookie_name, $token, $exp);
	}

	private function decode_remember_me_cookie(Controller $ctl, $cookie) {
		$decoded = $ctl->decrypt((string) $cookie);
		if (!is_string($decoded) || $decoded === "") {
			return "";
		}
		$data = json_decode($decoded, true);
		if (!is_array($data)) {
			return "";
		}
		$exp = (int) ($data["exp"] ?? 0);
		if ($exp <= time()) {
			return "";
		}
		$login_id = (string) ($data["login_id"] ?? "");
		if ($login_id === "") {
			return "";
		}
		$ua_hash = (string) ($data["ua"] ?? "");
		$ua = (string) ($_SERVER["HTTP_USER_AGENT"] ?? "");
		if ($ua_hash === "" || !hash_equals($ua_hash, hash("sha256", $ua))) {
			return "";
		}
		return $login_id;
	}
}

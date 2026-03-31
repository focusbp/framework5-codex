<?php

class codex_terminal {
	public function run(Controller $ctl) {
			try {
				$token_ttl = 900;
				$terminal_ws_path = $this->get_terminal_ws_path($ctl);
				$terminal_ws_token = $this->issue_ws_token($ctl, $token_ttl, "terminal");
				$initial_input = (string) $ctl->get_session("codex_terminal_initial_input");
				$ctl->set_session("codex_terminal_initial_input", "");
				$ctl->assign("codex_terminal_ws_path", $terminal_ws_path);
				$ctl->assign("codex_terminal_ws_token", $terminal_ws_token);
				$ctl->assign("codex_terminal_token_ttl", $token_ttl);
				$ctl->assign("codex_terminal_initial_input", $initial_input);
				$ctl->show_multi_dialog("codex_terminal", "terminal.tpl", $ctl->t("codex_terminal.dialog.title"), 980, "_fixed_bar.tpl");
		} catch (Throwable $e) {
			$ctl->show_notification_text($ctl->t("codex_terminal.unavailable"), 3, "#950000", "#FFF", 20, 760);
		}
	}

	public function close_dialog(Controller $ctl) {
		$ctl->close_multi_dialog("codex_terminal");
	}

	public function reopen_dialog(Controller $ctl) {
		$ctl->close_multi_dialog("codex_terminal");
		$this->run($ctl);
	}

	public function reflesh_all_screen(Controller $ctl) {
		$ctl->reload_menu();
		$ctl->reload_work_area();
		$ctl->reload_side_panel();
	}

	private function get_terminal_ws_path(Controller $ctl) {
		return "/codex-terminal-ws";
	}

	private function get_shared_secret(Controller $ctl) {
		$env = getenv("CODEX_TERMINAL_SHARED_SECRET");
		if (is_string($env) && $env !== "") {
			return $env;
		}
		throw new Exception("CODEX_TERMINAL_SHARED_SECRET is required. Set environment variable CODEX_TERMINAL_SHARED_SECRET.");
	}

	private function issue_ws_token(Controller $ctl, $ttl_sec, $scope) {
		$ttl = (int) $ttl_sec;
		if ($ttl <= 0) {
			$ttl = 900;
		}

		$now = time();
		$payload = [
			"sub" => (string) $ctl->get_login_id(),
			"sid" => (string) session_id(),
			"wid" => (string) $ctl->get_windowcode(),
			"scope" => (string) $scope,
			"iat" => $now,
			"exp" => $now + $ttl
		];

		$payload_json = json_encode($payload, JSON_UNESCAPED_SLASHES);
		if ($payload_json === false) {
			$payload_json = "{}";
		}
		$payload_b64 = $this->base64url_encode($payload_json);
		$sig = hash_hmac("sha256", $payload_b64, $this->get_shared_secret($ctl), true);
		$sig_b64 = $this->base64url_encode($sig);
		return $payload_b64 . "." . $sig_b64;
	}

	private function base64url_encode($data) {
		$encoded = base64_encode((string) $data);
		$encoded = str_replace("+", "-", $encoded);
		$encoded = str_replace("/", "_", $encoded);
		return rtrim($encoded, "=");
	}
}

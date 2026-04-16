<?php

class webhook_rule {

	private $fmt_rule;
	private $match_type_opt = [
		"exact" => "Exact",
		"contains" => "Contains",
		"regex" => "Regex",
		"data_type" => "Data Type",
		"unmatch" => "Unmatch",
	];
	private $data_type_keyword_opt = [
		"[image]",
		"[sticker]",
		"[follow]",
		"[getting_member]",
	];
	private $channel_opt = [
		0 => "LINE",
	];
	private $enabled_opt = [
		0 => "Disabled",
		1 => "Enabled",
	];

	function __construct(Controller $ctl) {
		$this->fmt_rule = $ctl->db("webhook_rule", "webhook_rule");
		$ctl->assign("match_type_opt", [
			"exact" => $ctl->t("webhook_rule.match_type.exact"),
			"contains" => $ctl->t("webhook_rule.match_type.contains"),
			"regex" => $ctl->t("webhook_rule.match_type.regex"),
			"data_type" => $ctl->t("webhook_rule.match_type.data_type"),
			"unmatch" => $ctl->t("webhook_rule.match_type.unmatch"),
		]);
		$ctl->assign("channel_opt", $this->channel_opt);
		$ctl->assign("enabled_opt", [
			0 => $ctl->t("common.disabled"),
			1 => $ctl->t("common.enabled"),
		]);
	}

	function page(Controller $ctl) {
		$items = $this->fmt_rule->getall("sort", SORT_ASC);
		$ctl->assign("items", $items);
		$ctl->reload_area("#tabs-webhook", "index.tpl");
	}

	function add(Controller $ctl) {
		$post = $ctl->POST();
		if (!isset($post["match_type"])) {
			$post["match_type"] = "exact";
		}
		if (!isset($post["channel"])) {
			$post["channel"] = 0;
		}
		if (!isset($post["enabled"])) {
			$post["enabled"] = 1;
		}
		$ctl->assign("post", $post);
		$ctl->show_multi_dialog("webhook_rule_add", "add.tpl", $ctl->t("webhook_rule.dialog.add"), 900, true, true);
	}

	function add_exe(Controller $ctl) {
		$post = $ctl->POST();
		$post = $this->normalize_post($post);
		$errors = $this->validate_rule($ctl, $post, "add");
		if (count($errors) > 0) {
			$ctl->clear_error_message();
			foreach ($errors as $field => $message) {
				$ctl->res_error_message($field, $message);
			}
			return;
		}

		$post["sort"] = $this->next_sort();
		$post["reply_template"] = "";
		$post["updated_at"] = time();
		$this->fmt_rule->insert($post);

		$ctl->close_multi_dialog("webhook_rule_add");
		$this->page($ctl);
	}

	function edit(Controller $ctl) {
		$id = (int) $ctl->POST("id");
		$data = $this->fmt_rule->get($id);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("webhook_rule_edit_" . $id, "edit.tpl", $ctl->t("webhook_rule.dialog.edit"), 900, true, true);
	}

	function edit_exe(Controller $ctl) {
		$post = $ctl->POST();
		$post = $this->normalize_post($post);
		$id = (int) ($post["id"] ?? 0);
		$errors = $this->validate_rule($ctl, $post, "edit");
		if (count($errors) > 0) {
			$ctl->clear_error_message();
			foreach ($errors as $field => $message) {
				$ctl->res_error_message($field, $message);
			}
			return;
		}

		$data = $this->fmt_rule->get($id);
		$data["channel"] = trim((string) ($post["channel"] ?? ""));
		$data["keyword"] = trim((string) ($post["keyword"] ?? ""));
		$data["match_type"] = trim((string) ($post["match_type"] ?? "exact"));
		$data["action_class"] = trim((string) ($post["action_class"] ?? ""));
		$data["enabled"] = isset($post["enabled"]) ? (int) $post["enabled"] : 1;
		$data["reply_template"] = "";
		$data["updated_at"] = time();
		$this->fmt_rule->update($data);

		$ctl->close_multi_dialog("webhook_rule_edit_" . $id);
		$this->page($ctl);
	}

	function delete(Controller $ctl) {
		$id = (int) $ctl->POST("id");
		$data = $this->fmt_rule->get($id);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("webhook_rule_delete_" . $id, "delete.tpl", $ctl->t("webhook_rule.dialog.delete"), 500, true, true);
	}

	function delete_exe(Controller $ctl) {
		$id = (int) $ctl->POST("id");
		$this->fmt_rule->delete($id);
		$ctl->close_multi_dialog("webhook_rule_delete_" . $id);
		$this->page($ctl);
	}

	function sort(Controller $ctl) {
		$post = $ctl->POST();
		$logArr = explode(',', (string) ($post["log"] ?? ""));
		$c = 0;
		foreach ($logArr as $id) {
			$id = (int) $id;
			if ($id <= 0) {
				continue;
			}
			$d = $this->fmt_rule->get($id);
			if (empty($d)) {
				continue;
			}
			$d["sort"] = $c;
			$d["updated_at"] = time();
			$this->fmt_rule->update($d);
			$c++;
		}
	}

	private function validate_rule(Controller $ctl, $post, $mode) {
		$errors = [];
		$id = (int) ($post["id"] ?? 0);
		$channel = (string) ($post["channel"] ?? "");
		$keyword = trim((string) ($post["keyword"] ?? ""));
		$match_type = trim((string) ($post["match_type"] ?? ""));
		$action_class = trim((string) ($post["action_class"] ?? ""));

		if (!array_key_exists((int) $channel, $this->channel_opt)) {
			$errors["channel"] = $ctl->t("webhook_rule.validation.channel_invalid");
		}
		if ($keyword === "" && $match_type !== "unmatch") {
			$errors["keyword"] = $ctl->t("webhook_rule.validation.keyword_required");
		}
		if (!isset($this->match_type_opt[$match_type])) {
			$errors["match_type"] = $ctl->t("webhook_rule.validation.match_type_invalid");
		}
		if ($match_type === "data_type" && !$this->is_valid_data_type_keyword($keyword)) {
			$errors["keyword"] = $ctl->t("webhook_rule.validation.data_type_keyword");
		}
		if ($match_type === "unmatch") {
			$duplicate_unmatch = $this->find_duplicate_unmatch_rule($ctl, $channel, $mode === "edit" ? $id : 0);
			if ($duplicate_unmatch !== null) {
				$errors["match_type"] = $ctl->t("webhook_rule.validation.unmatch_duplicate");
			}
		}
		if ($action_class === "") {
			$errors["action_class"] = $ctl->t("webhook_rule.validation.action_class_required");
		}
		if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $action_class)) {
			$errors["action_class"] = $ctl->t("webhook_rule.validation.action_class_format");
		}

		$key = $channel . "||" . $keyword;
		$is_unique = $ctl->validate_duplicate(
			"webhook_rule",
			["channel", "keyword"],
			[$channel, $keyword],
			$mode === "edit" ? $id : 0,
			"webhook_rule"
		);
		if (!$is_unique) {
			$errors["keyword"] = $ctl->t("webhook_rule.validation.duplicate");
		}

		return $errors;
	}

	private function normalize_post(array $post): array {
		$match_type = trim((string)($post["match_type"] ?? "exact"));
		$keyword = trim((string)($post["keyword"] ?? ""));
		if ($match_type === "data_type") {
			$token = $this->normalize_data_type_keyword($keyword);
			if ($token !== "") {
				$post["keyword"] = "[" . $token . "]";
			}
		} else if ($match_type === "unmatch") {
			$post["keyword"] = "[unmatch]";
		}
		return $post;
	}

	private function find_duplicate_unmatch_rule(Controller $ctl, string $channel, int $exclude_id = 0): ?array {
		$list = $ctl->db("webhook_rule", "webhook_rule")->select("channel", $channel, true, "AND", "sort", SORT_ASC);
		if (!is_array($list)) {
			return null;
		}
		foreach ($list as $one) {
			if ((int) ($one["id"] ?? 0) === $exclude_id) {
				continue;
			}
			if (trim((string) ($one["match_type"] ?? "")) === "unmatch") {
				return $one;
			}
		}
		return null;
	}

	private function is_valid_data_type_keyword(string $keyword): bool {
		$token = $this->normalize_data_type_keyword($keyword);
		if ($token === "") {
			return false;
		}
		return in_array("[" . $token . "]", $this->data_type_keyword_opt, true);
	}

	private function normalize_data_type_keyword(string $keyword): string {
		$token = trim(mb_strtolower($keyword));
		if ($token === "") {
			return "";
		}
		if (substr($token, 0, 1) === "[" && substr($token, -1) === "]") {
			$token = substr($token, 1, -1);
		}
		return trim($token);
	}

	private function next_sort() {
		$list = $this->fmt_rule->getall("sort", SORT_DESC);
		if (count($list) === 0) {
			return 0;
		}
		return (int) ($list[0]["sort"] ?? 0) + 1;
	}

}

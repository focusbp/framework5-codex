<?php

class embed_app {

	private $fmt_embed_app;
	private $enabled_opt = [
		0 => "Disabled",
		1 => "Enabled",
	];

	function __construct(Controller $ctl) {
		$this->fmt_embed_app = $ctl->db("embed_app", "embed_app");
		$ctl->assign("enabled_opt", [
			0 => $ctl->t("common.disabled"),
			1 => $ctl->t("common.enabled"),
		]);
	}

	function page(Controller $ctl) {
		$items = $this->fmt_embed_app->getall("sort", SORT_ASC);
		$loader_url = $ctl->get_APP_URL("embed_app_runtime", "loader_js");
		foreach ($items as &$item) {
			$route_url = $ctl->get_APP_URL("embed_app_runtime", "route", ["embed_key" => $item["embed_key"]]);
			$item["loader_url"] = $loader_url;
			$item["route_url"] = $route_url;
			$item["snippet_tag"] = $this->build_snippet_tag($loader_url, $route_url, $item["embed_key"]);
		}
		$ctl->assign("items", $items);
		$ctl->reload_area("#tabs-embed-app", "index.tpl");
	}

	function add(Controller $ctl) {
		$post = $ctl->POST();
		if (!isset($post["enabled"])) {
			$post["enabled"] = 1;
		}
		$ctl->assign("post", $post);
		$ctl->show_multi_dialog("embed_app_add", "add.tpl", $ctl->t("embed_app.dialog.add"), 900, true, true);
	}

	function add_exe(Controller $ctl) {
		$post = $this->normalize_post($ctl->POST());
		$errors = $this->validate_embed_app($ctl, $post, "add");
		if (count($errors) > 0) {
			$ctl->clear_error_message();
			foreach ($errors as $field => $message) {
				$ctl->res_error_message($field, $message);
			}
			return;
		}
		$post["sort"] = $this->next_sort();
		$post["created_at"] = time();
		$post["updated_at"] = time();
		$this->fmt_embed_app->insert($post);

		$ctl->close_multi_dialog("embed_app_add");
		$this->page($ctl);
	}

	function edit(Controller $ctl) {
		$id = (int) $ctl->POST("id");
		$data = $this->fmt_embed_app->get($id);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("embed_app_edit_" . $id, "edit.tpl", $ctl->t("embed_app.dialog.edit"), 900, true, true);
	}

	function edit_exe(Controller $ctl) {
		$post = $this->normalize_post($ctl->POST());
		$id = (int) ($post["id"] ?? 0);
		$errors = $this->validate_embed_app($ctl, $post, "edit");
		if (count($errors) > 0) {
			$ctl->clear_error_message();
			foreach ($errors as $field => $message) {
				$ctl->res_error_message($field, $message);
			}
			return;
		}
		$data = $this->fmt_embed_app->get($id);
		$data["embed_key"] = $post["embed_key"];
		$data["title"] = $post["title"];
		$data["class_name"] = $post["class_name"];
		$data["enabled"] = (int) $post["enabled"];
		$data["allowed_origins"] = $post["allowed_origins"];
		$data["updated_at"] = time();
		$this->fmt_embed_app->update($data);

		$ctl->close_multi_dialog("embed_app_edit_" . $id);
		$this->page($ctl);
	}

	function delete(Controller $ctl) {
		$id = (int) $ctl->POST("id");
		$data = $this->fmt_embed_app->get($id);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("embed_app_delete_" . $id, "delete.tpl", $ctl->t("embed_app.dialog.delete"), 500, true, true);
	}

	function delete_exe(Controller $ctl) {
		$id = (int) $ctl->POST("id");
		$this->fmt_embed_app->delete($id);
		$ctl->close_multi_dialog("embed_app_delete_" . $id);
		$this->page($ctl);
	}

	function sort(Controller $ctl) {
		$post = $ctl->POST();
		$logArr = explode(',', (string) $post["log"]);
		$c = 0;
		foreach ($logArr as $id) {
			$id = (int) $id;
			if ($id <= 0) {
				continue;
			}
			$d = $this->fmt_embed_app->get($id);
			if (empty($d)) {
				continue;
			}
			$d["sort"] = $c;
			$d["updated_at"] = time();
			$this->fmt_embed_app->update($d);
			$c++;
		}
	}

	function snippet_dialog(Controller $ctl) {
		$id = (int) $ctl->POST("id");
		$data = $this->fmt_embed_app->get($id);
		if (empty($data)) {
			return;
		}
		$this->assign_snippet_vars($ctl, $data);
		$ctl->show_multi_dialog("embed_app_snippet_" . $id, "snippet.tpl", $ctl->t("embed_app.dialog.snippet"), 980, true, true);
	}

	private function validate_embed_app(Controller $ctl, array $post, string $mode): array {
		$errors = [];
		$id = (int) ($post["id"] ?? 0);
		$embed_key = trim((string) ($post["embed_key"] ?? ""));
		$title = trim((string) ($post["title"] ?? ""));
		$class_name = trim((string) ($post["class_name"] ?? ""));

		if ($embed_key === "") {
			$errors["embed_key"] = $ctl->t("embed_app.validation.embed_key_required");
		} elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $embed_key)) {
			$errors["embed_key"] = $ctl->t("embed_app.validation.embed_key_format");
		}

		$is_unique = $ctl->validate_duplicate(
			"embed_app",
			["embed_key"],
			[$embed_key],
			$mode === "edit" ? $id : 0,
			"embed_app"
		);
		if (!$is_unique) {
			$errors["embed_key"] = $ctl->t("embed_app.validation.embed_key_exists");
		}

		if ($title === "") {
			$errors["title"] = $ctl->t("embed_app.validation.title_required");
		}
		if ($class_name === "") {
			$errors["class_name"] = $ctl->t("embed_app.validation.class_name_required");
		} elseif (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $class_name)) {
			$errors["class_name"] = $ctl->t("embed_app.validation.class_name_format");
		}
		return $errors;
	}

	private function normalize_post(array $post): array {
		$post["embed_key"] = trim((string) ($post["embed_key"] ?? ""));
		$post["title"] = trim((string) ($post["title"] ?? ""));
		$post["class_name"] = trim((string) ($post["class_name"] ?? ""));
		$post["allowed_origins"] = trim((string) ($post["allowed_origins"] ?? ""));
		$post["enabled"] = isset($post["enabled"]) ? (int) $post["enabled"] : 1;
		return $post;
	}

	private function next_sort(): int {
		$list = $this->fmt_embed_app->getall("sort", SORT_DESC);
		if (count($list) === 0) {
			return 0;
		}
		return (int) ($list[0]["sort"] ?? 0) + 1;
	}

	private function build_snippet_tag(string $loader_url, string $route_url, string $embed_key): string {
		$target_id = "embed-app-" . preg_replace('/[^a-zA-Z0-9_-]/', '-', $embed_key);
		$loader_url = htmlspecialchars($loader_url, ENT_QUOTES, 'UTF-8');
		$route_url = htmlspecialchars($route_url, ENT_QUOTES, 'UTF-8');
		$embed_key = htmlspecialchars($embed_key, ENT_QUOTES, 'UTF-8');

		return "<div id=\"" . $target_id . "\"></div>\n"
			. "<script src=\"" . $loader_url . "\" data-target=\"#" . $target_id . "\" data-boot-url=\"" . $route_url . "\" data-embed-key=\"" . $embed_key . "\" defer></script>";
	}

	private function assign_snippet_vars(Controller $ctl, array $data): void {
		$loader_url = $ctl->get_APP_URL("embed_app_runtime", "loader_js");
		$route_url = $ctl->get_APP_URL("embed_app_runtime", "route", ["embed_key" => $data["embed_key"]]);
		$ctl->assign("data", $data);
		$ctl->assign("loader_url", $loader_url);
		$ctl->assign("route_url", $route_url);
		$ctl->assign(
			"snippet_code",
			$this->build_snippet_tag($loader_url, $route_url, $data["embed_key"])
		);
	}

}

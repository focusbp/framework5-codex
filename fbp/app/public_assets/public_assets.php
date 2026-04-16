<?php

class public_assets {

	private $fmt_assets;
	private $enabled_opt = [
		0 => "Hide",
		1 => "Show",
	];
	private $asset_dir;

	function __construct(Controller $ctl) {
		$this->fmt_assets = $ctl->db("public_assets", "public_assets");
		$ctl->assign("enabled_opt", [
			0 => $ctl->t("common.hide"),
			1 => $ctl->t("common.show"),
		]);
		$this->asset_dir = dirname(__FILE__) . "/../../../classes/data/public_pages/assets";
		if (!is_dir($this->asset_dir)) {
			mkdir($this->asset_dir, 0777, true);
		}
	}

	function page(Controller $ctl) {
		$items = $this->fmt_assets->getall("sort", SORT_ASC);
		foreach ($items as &$item) {
			$item["preview_url"] = $ctl->get_APP_URL("public_asset_media", "view", ["key" => (string) ($item["asset_key"] ?? "")]);
		}
		unset($item);
		$ctl->assign("items", $items);
		$ctl->reload_area("#tabs-public-assets", "index.tpl");
	}

	function add(Controller $ctl) {
		$post = $ctl->POST();
		if (!isset($post["enabled"])) {
			$post["enabled"] = 1;
		}
		$ctl->assign("post", $post);
		$ctl->show_multi_dialog("public_assets_add", "add.tpl", $ctl->t("public_assets.dialog.add"), 860, true, true);
	}

	function add_exe(Controller $ctl) {
		$res = $this->store_uploaded_asset($ctl, 1);
		if (!($res["ok"] ?? false)) {
			$ctl->clear_error_message();
			foreach (($res["errors"] ?? []) as $field => $message) {
				$ctl->res_error_message($field, $message);
			}
			return;
		}

		$ctl->close_multi_dialog("public_assets_add");
		$this->page($ctl);
	}

	function edit(Controller $ctl) {
		$id = (int) $ctl->POST("id");
		$data = $this->fmt_assets->get($id);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("public_assets_edit_" . $id, "edit.tpl", $ctl->t("public_assets.dialog.edit"), 860, true, true);
	}

	function edit_exe(Controller $ctl) {
		$post = $this->normalize_post($ctl->POST());
		$id = (int) ($post["id"] ?? 0);
		$data = $this->fmt_assets->get($id);
		if (empty($data)) {
			return;
		}
		$post["asset_key"] = (string) ($data["asset_key"] ?? "");
		$require_file = $this->has_uploaded_file("asset_file");
		$errors = $this->validate_asset($ctl, $post, "edit", false);
		if (count($errors) > 0) {
			$ctl->clear_error_message();
			foreach ($errors as $field => $message) {
				$ctl->res_error_message($field, $message);
			}
			return;
		}

		$data["title"] = "";
		$data["asset_key"] = $post["asset_key"];
		$data["enabled"] = (int) $post["enabled"];
		if ($require_file) {
			$original_filename = $this->get_original_uploaded_filename($ctl, "asset_file");
			$mime_type = $this->detect_uploaded_mime_type("asset_file");
			$extension = $this->resolve_uploaded_extension($ctl, "asset_file", $mime_type);
			$stored_filename = $this->build_stored_filename($extension);
			$this->move_uploaded_asset_to_public_assets($ctl, "asset_file", $stored_filename);
			$this->delete_asset_file((string) ($data["stored_filename"] ?? ""));
			$data["stored_filename"] = $stored_filename;
			$data["original_filename"] = $original_filename;
			$data["mime_type"] = $mime_type;
		}
		$data["updated_at"] = time();
		$this->fmt_assets->update($data);

		$ctl->close_multi_dialog("public_assets_edit_" . $id);
		$this->page($ctl);
	}

	function delete(Controller $ctl) {
		$id = (int) $ctl->POST("id");
		$data = $this->fmt_assets->get($id);
		if (is_array($data) && count($data) > 0) {
			$data["preview_url"] = $ctl->get_APP_URL("public_asset_media", "view", ["key" => (string) ($data["asset_key"] ?? "")]);
		}
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("public_assets_delete_" . $id, "delete.tpl", $ctl->t("public_assets.dialog.delete"), 520, true, true);
	}

	function delete_exe(Controller $ctl) {
		$id = (int) $ctl->POST("id");
		$data = $this->fmt_assets->get($id);
		if (is_array($data) && count($data) > 0) {
			$this->delete_asset_file((string) ($data["stored_filename"] ?? ""));
			$this->fmt_assets->delete($id);
		}
		$ctl->close_multi_dialog("public_assets_delete_" . $id);
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
			$d = $this->fmt_assets->get($id);
			if (empty($d)) {
				continue;
			}
			$d["sort"] = $c;
			$d["updated_at"] = time();
			$this->fmt_assets->update($d);
			$c++;
		}
	}

	public function store_uploaded_asset(Controller $ctl, int $enabled = 1): array {
		$post = $this->normalize_post($ctl->POST());
		$post["title"] = "";
		$post["asset_key"] = $this->build_random_asset_key();
		$post["enabled"] = $enabled === 1 ? 1 : 0;
		$errors = $this->validate_asset($ctl, $post, "add", true);
		if (count($errors) > 0) {
			return [
				"ok" => false,
				"errors" => $errors
			];
		}
		try {
			$original_filename = $this->get_original_uploaded_filename($ctl, "asset_file");
			$mime_type = $this->detect_uploaded_mime_type("asset_file");
			$extension = $this->resolve_uploaded_extension($ctl, "asset_file", $mime_type);
			$stored_filename = $this->build_stored_filename($extension);
			$this->move_uploaded_asset_to_public_assets($ctl, "asset_file", $stored_filename);

			$post["stored_filename"] = $stored_filename;
			$post["original_filename"] = $original_filename;
			$post["mime_type"] = $mime_type;
			$post["sort"] = $this->next_sort();
			$post["updated_at"] = time();
			$this->fmt_assets->insert($post);
			return [
				"ok" => true,
				"data" => $post
			];
		} catch (Throwable $e) {
			return [
				"ok" => false,
				"errors" => [
					"asset_file" => $ctl->t("public_assets.validation.store_failed")
				]
			];
		}
	}

	private function validate_asset(Controller $ctl, array $post, string $mode, bool $require_file): array {
		$errors = [];
		$id = (int) ($post["id"] ?? 0);
		$asset_key = $this->normalize_asset_key((string) ($post["asset_key"] ?? ""));
		if ($asset_key === "") {
			$errors["asset_key"] = $ctl->t("public_assets.validation.asset_key_required");
		} elseif (!preg_match('/^[a-z][a-z0-9_]*$/', $asset_key)) {
			$errors["asset_key"] = $ctl->t("public_assets.validation.asset_key_format");
		}
		$is_unique = $ctl->validate_duplicate(
			"public_assets",
			["asset_key"],
			[$asset_key],
			$mode === "edit" ? $id : 0,
			"public_assets"
		);
		if (!$is_unique) {
			$errors["asset_key"] = $ctl->t("public_assets.validation.asset_key_exists");
		}
		if ($require_file && !$this->has_uploaded_file("asset_file")) {
			$errors["asset_file"] = $ctl->t("public_assets.validation.asset_file_required");
		}
		if ($this->has_uploaded_file("asset_file")) {
			$mime_type = $this->detect_uploaded_mime_type("asset_file");
			if (!$this->is_allowed_image_mime($mime_type)) {
				$errors["asset_file"] = $ctl->t("public_assets.validation.asset_file_image");
			}
		}
		return $errors;
	}

	private function normalize_post(array $post): array {
		return [
			"id" => (int) ($post["id"] ?? 0),
			"title" => "",
			"asset_key" => $this->normalize_asset_key((string) ($post["asset_key"] ?? "")),
			"stored_filename" => trim((string) ($post["stored_filename"] ?? "")),
			"original_filename" => trim((string) ($post["original_filename"] ?? "")),
			"mime_type" => trim((string) ($post["mime_type"] ?? "")),
			"enabled" => isset($post["enabled"]) ? (int) $post["enabled"] : 1,
		];
	}

	private function normalize_asset_key(string $key): string {
		$key = strtolower(trim($key));
		$key = preg_replace('/[^a-z0-9_]+/', '_', $key);
		$key = trim((string) $key, '_');
		if ($key === '') {
			return '';
		}
		if (preg_match('/^[0-9]/', $key)) {
			$key = 'asset_' . $key;
		}
		return $key;
	}

	private function suggest_asset_key(string $title): string {
		$key = $this->normalize_asset_key($title);
		if ($key !== '') {
			return $key;
		}
		return 'asset_' . date('Ymd_His');
	}

	private function build_random_asset_key(): string {
		for ($i = 0; $i < 10; $i++) {
			$key = "asset_" . bin2hex(random_bytes(6));
			$list = $this->fmt_assets->select("asset_key", $key);
			if (!is_array($list) || count($list) === 0) {
				return $key;
			}
		}
		return "asset_" . date("YmdHis") . "_" . bin2hex(random_bytes(2));
	}

	private function next_sort(): int {
		$list = $this->fmt_assets->getall("sort", SORT_DESC);
		if (count($list) === 0) {
			return 0;
		}
		return (int) ($list[0]["sort"] ?? 0) + 1;
	}

	private function get_original_uploaded_filename(Controller $ctl, string $post_name): string {
		return basename((string) $ctl->get_posted_filename($post_name));
	}

	private function has_uploaded_file(string $post_name): bool {
		if (empty($_FILES[$post_name]) || !is_array($_FILES[$post_name])) {
			return false;
		}
		$file = $_FILES[$post_name];
		$error = (int) ($file["error"] ?? UPLOAD_ERR_NO_FILE);
		if ($error !== UPLOAD_ERR_OK) {
			return false;
		}
		$size = (int) ($file["size"] ?? 0);
		if ($size <= 0) {
			return false;
		}
		$tmp = (string) ($file["tmp_name"] ?? "");
		if ($tmp === "" || !is_file($tmp)) {
			return false;
		}
		return true;
	}

	private function detect_uploaded_mime_type(string $post_name): string {
		$tmp = $_FILES[$post_name]['tmp_name'] ?? '';
		if (!is_string($tmp) || $tmp === '' || !is_file($tmp)) {
			return '';
		}
		if (class_exists('finfo')) {
			$fi = new finfo(FILEINFO_MIME_TYPE);
			$mt = $fi->file($tmp);
			if (is_string($mt) && $mt !== '') {
				return $mt;
			}
		}
		return (string) ($_FILES[$post_name]['type'] ?? '');
	}

	private function is_allowed_image_mime(string $mime_type): bool {
		$allow = [
			'image/jpeg' => 1,
			'image/png' => 1,
			'image/gif' => 1,
			'image/webp' => 1,
			'image/svg+xml' => 1,
		];
		return isset($allow[strtolower(trim($mime_type))]);
	}

	private function resolve_uploaded_extension(Controller $ctl, string $post_name, string $mime_type): string {
		$ext = strtolower(trim((string) $ctl->get_posted_file_extention($post_name)));
		$map = [
			'jpeg' => 'jpg',
			'jpg' => 'jpg',
			'png' => 'png',
			'gif' => 'gif',
			'webp' => 'webp',
			'svg' => 'svg',
		];
		if (isset($map[$ext])) {
			return $map[$ext];
		}
		$mime_map = [
			'image/jpeg' => 'jpg',
			'image/png' => 'png',
			'image/gif' => 'gif',
			'image/webp' => 'webp',
			'image/svg+xml' => 'svg',
		];
		return $mime_map[strtolower(trim($mime_type))] ?? 'bin';
	}

	private function build_stored_filename(string $extension): string {
		$extension = strtolower(trim($extension));
		if ($extension === '') {
			$extension = 'bin';
		}
		try {
			$rand = bin2hex(random_bytes(4));
		} catch (Exception $e) {
			$rand = substr(md5(uniqid('', true)), 0, 8);
		}
		return 'asset_' . date('Ymd_His') . '_' . $rand . '.' . $extension;
	}

	private function move_uploaded_asset_to_public_assets(Controller $ctl, string $post_name, string $stored_filename): void {
		$tmp_saved = 'public_asset_tmp_' . uniqid('', true);
		$ctl->save_posted_file($post_name, $tmp_saved);
		$tmp_path = $ctl->get_saved_filepath($tmp_saved);
		if (!is_file($tmp_path)) {
			throw new Exception('Temporary upload file was not saved.');
		}
		$target_path = $this->asset_dir . '/' . basename($stored_filename);
		if (!copy($tmp_path, $target_path)) {
			$ctl->remove_saved_file($tmp_saved);
			throw new Exception('Failed to copy asset file.');
		}
		$ctl->remove_saved_file($tmp_saved);
	}

	private function delete_asset_file(string $stored_filename): void {
		$stored_filename = basename($stored_filename);
		if ($stored_filename === '') {
			return;
		}
		$path = $this->asset_dir . '/' . $stored_filename;
		if (is_file($path)) {
			unlink($path);
		}
	}

}

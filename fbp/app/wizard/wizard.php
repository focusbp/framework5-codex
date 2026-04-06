<?php

class wizard {

	function run(Controller $ctl) {
		$this->show_home($ctl);
	}

	function close_dialog(Controller $ctl) {
		$ctl->close_multi_dialog("wizard");
	}

	function open_professional_mode(Controller $ctl) {
		$ctl->close_multi_dialog("wizard");
		$ctl->invoke("run", [], "codex_terminal");
	}

	function open_note_wizard(Controller $ctl) {
		$state = [
			"note_action" => "add"
		];
		$ctl->set_session("wizard_note_state", $state);
		$this->show_note_step_select($ctl, $state);
	}

	function submit_note_action_next(Controller $ctl) {
		$action = trim((string) $ctl->POST("note_action"));
		if (!in_array($action, ["add", "update", "delete", "child_add", "parent_child"], true)) {
			$ctl->res_error_message("note_action", $ctl->t("wizard.validation.choose_action"));
			return;
		}
		$state = [
			"note_action" => $action
		];
		$ctl->set_session("wizard_note_state", $state);
		if ($action === "add") {
			$this->open_table_create_wizard($ctl);
			return;
		}
		if ($action === "update") {
			$this->open_note_edit_wizard($ctl);
			return;
		}
		if ($action === "child_add") {
			$this->open_child_note_create_wizard($ctl);
			return;
		}
		if ($action === "parent_child") {
			$this->open_parent_child_note_wizard($ctl);
			return;
		}
		$this->open_note_delete_wizard($ctl);
	}

	function open_child_note_create_wizard(Controller $ctl) {
		$state = [
			"project_name" => $this->detect_current_project_name(),
			"purpose" => "",
			"note_title" => "",
			"menu_name" => "",
			"field_mode" => "auto",
			"manual_fields_text" => "",
			"create_mode" => "child",
			"parent_tb_name" => "",
			"parent_db_id" => "",
			"parent_menu_name" => ""
		];
		$this->save_table_create_state($ctl, $state);
		$this->show_table_create_parent_step($ctl, $this->get_table_create_state($ctl));
	}

	function submit_child_note_parent_next(Controller $ctl) {
		$parent_tb_name = $this->normalize_table_name((string) $ctl->POST("parent_tb_name"));
		if ($parent_tb_name === "") {
			$ctl->res_error_message("parent_tb_name", $ctl->t("wizard.validation.parent_note_required"));
			return;
		}
		$db = $this->find_db_row_by_tb_name($ctl, $parent_tb_name);
		if (!is_array($db) || count($db) === 0) {
			$ctl->res_error_message("parent_tb_name", $ctl->t("wizard.validation.parent_note_not_found"));
			return;
		}
		$state = $this->get_table_create_state($ctl);
		$state["create_mode"] = "child";
		$state["parent_tb_name"] = $parent_tb_name;
		$state["parent_db_id"] = $this->normalize_single_id($db["id"] ?? "");
		$state["parent_menu_name"] = trim((string) ($db["menu_name"] ?? ""));
		$this->save_table_create_state($ctl, $state);
		$this->show_step_purpose($ctl, $this->get_table_create_state($ctl));
	}

	function back_to_table_create_parent(Controller $ctl) {
		$this->show_table_create_parent_step($ctl, $this->get_table_create_state($ctl));
	}

	function open_note_edit_wizard(Controller $ctl) {
		$state = [
			"target_tb_name" => "",
			"db_id" => "",
			"menu_name" => "",
			"description" => "",
			"show_menu" => "1",
			"sortkey" => "id",
			"sort_order" => "4",
			"edit_width" => "800",
			"list_type" => "0",
			"show_duplicate" => "0",
			"show_id" => "0",
			"side_list_type" => "0",
			"cascade_delete_flag" => "0",
			"show_icon_on_parent_list" => "0",
			"has_parent_note" => 0
		];
		$this->save_note_edit_state($ctl, $state);
		$this->show_note_edit_step_table($ctl, $this->get_note_edit_state($ctl));
	}

	function submit_note_edit_table_next(Controller $ctl) {
		$tb_name = $this->normalize_table_name((string) $ctl->POST("target_tb_name"));
		if ($tb_name === "") {
			$ctl->res_error_message("target_tb_name", $ctl->t("wizard.validation.target_note_required"));
			return;
		}
		$db = $this->find_db_row_by_tb_name($ctl, $tb_name);
		if (!is_array($db) || count($db) === 0) {
			$ctl->res_error_message("target_tb_name", $ctl->t("wizard.validation.target_note_not_found"));
			return;
		}
		$list_type = (string) ($db["list_type"] ?? "0");
		if (!isset($this->get_note_list_type_options()[$list_type])) {
			$list_type = "0";
		}
		if ((int) $list_type === 1) {
			$this->ensure_note_edit_sort_field($ctl, (int) ($db["id"] ?? 0));
		}
		$state = [
			"target_tb_name" => $tb_name,
			"db_id" => $this->normalize_single_id($db["id"] ?? ""),
			"menu_name" => trim((string) ($db["menu_name"] ?? "")),
			"description" => trim((string) ($db["description"] ?? "")),
			"show_menu" => (string) ($db["show_menu"] ?? "1"),
			"sortkey" => trim((string) ($db["sortkey"] ?? "id")),
			"sort_order" => (string) ($db["sort_order"] ?? "4"),
			"edit_width" => (string) ((int) ($db["edit_width"] ?? 800)),
			"list_type" => $list_type,
			"show_duplicate" => (string) ($db["show_duplicate"] ?? "0"),
			"show_id" => (string) ($db["show_id"] ?? "0"),
			"side_list_type" => (string) ($db["side_list_type"] ?? "0"),
			"cascade_delete_flag" => (string) ($db["cascade_delete_flag"] ?? "0"),
			"show_icon_on_parent_list" => (string) ($db["show_icon_on_parent_list"] ?? "0"),
			"has_parent_note" => ((int) ($db["parent_tb_id"] ?? 0)) > 0 ? 1 : 0
		];
		$this->save_note_edit_state($ctl, $state);
		$this->show_note_edit_step_basic($ctl, $this->get_note_edit_state($ctl));
	}

	function submit_note_edit_basic_next(Controller $ctl) {
		$state = $this->get_note_edit_state($ctl);
		$tb_name = $this->normalize_table_name((string) ($state["target_tb_name"] ?? ""));
		if ($tb_name === "") {
			$ctl->show_notification_text($ctl->t("wizard.notification.reselect_target_note"), 3);
			return;
		}

		$menu_name = trim((string) $ctl->POST("menu_name"));
		if ($menu_name === "") {
			$ctl->res_error_message("menu_name", $ctl->t("wizard.validation.note_name_required"));
			return;
		}
		$description = trim((string) $ctl->POST("description"));
		$show_menu = (string) $ctl->POST("show_menu");
		if (!isset($this->get_note_show_menu_options()[$show_menu])) {
			$ctl->res_error_message("show_menu", $ctl->t("wizard.validation.show_menu_required"));
			return;
		}
		$sortkey = trim((string) $ctl->POST("sortkey"));
		$sortkey_opt = $this->get_note_sortkey_options($ctl, $tb_name);
		if (!isset($sortkey_opt[$sortkey])) {
			$ctl->res_error_message("sortkey", $ctl->t("wizard.validation.sortkey_required"));
			return;
		}
		$sort_order = (string) $ctl->POST("sort_order");
		if (!isset($this->get_note_sort_order_options()[$sort_order])) {
			$ctl->res_error_message("sort_order", $ctl->t("wizard.validation.sort_order_required"));
			return;
		}
		$edit_width = trim((string) $ctl->POST("edit_width"));
		if (!$this->is_valid_note_width($edit_width)) {
			$ctl->res_error_message("edit_width", $ctl->t("wizard.validation.dialog_width_range"));
			return;
		}

		$state["menu_name"] = $menu_name;
		$state["description"] = $description;
		$state["show_menu"] = $show_menu;
		$state["sortkey"] = $sortkey;
		$list_type = (string) $ctl->POST("list_type");
		if (!isset($this->get_note_list_type_options()[$list_type])) {
			$ctl->res_error_message("list_type", $ctl->t("wizard.validation.list_type_required"));
			return;
		}
		$show_duplicate = (string) $ctl->POST("show_duplicate");
		if (!isset($this->get_note_toggle_options()[$show_duplicate])) {
			$ctl->res_error_message("show_duplicate", $ctl->t("wizard.validation.show_duplicate_required"));
			return;
		}
		$show_id = (string) $ctl->POST("show_id");
		if (!isset($this->get_note_toggle_options()[$show_id])) {
			$ctl->res_error_message("show_id", $ctl->t("wizard.validation.show_id_required"));
			return;
		}
		$state["sort_order"] = $sort_order;
		$state["edit_width"] = $edit_width;
		$state["list_type"] = $list_type;
		$state["show_duplicate"] = $show_duplicate;
		$state["show_id"] = $show_id;
		if ((int) ($state["has_parent_note"] ?? 0) > 0) {
			$side_list_type = (string) $ctl->POST("side_list_type");
			if (!isset($this->get_note_side_list_type_options()[$side_list_type])) {
				$ctl->res_error_message("side_list_type", $ctl->t("wizard.validation.side_list_type_required"));
				return;
			}
			$cascade_delete_flag = (string) $ctl->POST("cascade_delete_flag");
			if (!isset($this->get_cascade_delete_flag_options()[$cascade_delete_flag])) {
				$ctl->res_error_message("cascade_delete_flag", $ctl->t("wizard.validation.cascade_delete_required"));
				return;
			}
			$show_icon_on_parent_list = (string) $ctl->POST("show_icon_on_parent_list");
			if (!isset($this->get_note_parent_icon_options()[$show_icon_on_parent_list])) {
				$ctl->res_error_message("show_icon_on_parent_list", $ctl->t("wizard.validation.parent_icon_required"));
				return;
			}
			$state["side_list_type"] = $side_list_type;
			$state["cascade_delete_flag"] = $cascade_delete_flag;
			$state["show_icon_on_parent_list"] = $show_icon_on_parent_list;
		}
		$this->save_note_edit_state($ctl, $state);
		$db_id = $this->normalize_single_id($state["db_id"] ?? "");
		if ($db_id === "") {
			$ctl->show_notification_text($ctl->t("wizard.notification.reselect_target_note"), 3);
			return;
		}
		$db = $ctl->db("db", "db")->get((int) $db_id);
		if (!is_array($db) || count($db) === 0) {
			$ctl->show_notification_text($ctl->t("wizard.validation.target_note_not_found"), 3);
			return;
		}
		$db["menu_name"] = $state["menu_name"];
		$db["description"] = $state["description"];
		$db["show_menu"] = (int) $state["show_menu"];
		$db["sortkey"] = $state["sortkey"];
		$db["sort_order"] = (int) $state["sort_order"];
		$db["edit_width"] = (int) $state["edit_width"];
		$db["list_type"] = (int) $state["list_type"];
		$db["show_duplicate"] = (int) $state["show_duplicate"];
		$db["show_id"] = (int) $state["show_id"];
		if ((int) ($state["has_parent_note"] ?? 0) > 0) {
			$db["side_list_type"] = (int) $state["side_list_type"];
			$db["cascade_delete_flag"] = (int) $state["cascade_delete_flag"];
			$db["show_icon_on_parent_list"] = (int) $state["show_icon_on_parent_list"];
		}
		$ctl->db("db", "db")->update($db);
		if ((int) $db["list_type"] === 1) {
			$this->ensure_note_edit_sort_field($ctl, (int) $db_id);
			$db["sortkey"] = "sort";
			$db["sort_order"] = 4;
			$ctl->db("db", "db")->update($db);
		}
		if ((int) $db["list_type"] === 2) {
			$this->ensure_note_edit_weekly_calendar_fields($ctl, (int) $db_id);
		}
		$this->reflesh_all_screen($ctl);
	}

	function back_to_note_select(Controller $ctl) {
		$state = $ctl->get_session("wizard_note_state");
		if (!is_array($state)) {
			$state = ["note_action" => "add"];
		}
		$this->show_note_step_select($ctl, $state);
	}

	function back_to_note_edit_table(Controller $ctl) {
		$this->show_note_edit_step_table($ctl, $this->get_note_edit_state($ctl));
	}

	function back_to_note_edit_basic(Controller $ctl) {
		$this->show_note_edit_step_basic($ctl, $this->get_note_edit_state($ctl));
	}

	function open_note_delete_wizard(Controller $ctl) {
		$state = [
			"target_tb_name" => "",
			"db_id" => "",
			"menu_name" => "",
			"description" => ""
		];
		$this->save_note_delete_state($ctl, $state);
		$this->show_note_delete_step_table($ctl, $this->get_note_delete_state($ctl));
	}

	function submit_note_delete_table_next(Controller $ctl) {
		$tb_name = $this->normalize_table_name((string) $ctl->POST("target_tb_name"));
		if ($tb_name === "") {
			$ctl->res_error_message("target_tb_name", $ctl->t("wizard.validation.delete_target_note_required"));
			return;
		}
		$db = $this->find_db_row_by_tb_name($ctl, $tb_name);
		if (!is_array($db) || count($db) === 0) {
			$ctl->res_error_message("target_tb_name", $ctl->t("wizard.validation.target_note_not_found"));
			return;
		}
		$state = [
			"target_tb_name" => $tb_name,
			"db_id" => $this->normalize_single_id($db["id"] ?? ""),
			"menu_name" => trim((string) ($db["menu_name"] ?? "")),
			"description" => trim((string) ($db["description"] ?? ""))
		];
		$this->save_note_delete_state($ctl, $state);
		$this->show_note_delete_preview($ctl, $this->get_note_delete_state($ctl));
	}

	function back_to_note_delete_table(Controller $ctl) {
		$this->show_note_delete_step_table($ctl, $this->get_note_delete_state($ctl));
	}

	function open_parent_child_note_wizard(Controller $ctl) {
		$state = [
			"child_tb_name" => "",
			"child_db_id" => "",
			"child_menu_name" => "",
			"parent_tb_name" => "",
			"parent_db_id" => "",
			"parent_menu_name" => "",
			"dropdown_item" => "id",
			"dropdown_item_display_type" => "field",
			"dropdown_item_template" => "",
			"list_width" => "800",
			"cascade_delete_flag" => "0"
		];
		$this->save_parent_child_note_state($ctl, $state);
		$this->show_parent_child_note_step_child($ctl, $this->get_parent_child_note_state($ctl));
	}

	function submit_parent_child_note_child_next(Controller $ctl) {
		$child_tb_name = $this->normalize_table_name((string) $ctl->POST("child_tb_name"));
		if ($child_tb_name === "") {
			$ctl->res_error_message("child_tb_name", $ctl->t("wizard.validation.child_note_required"));
			return;
		}
		$child_db = $this->find_db_row_by_tb_name($ctl, $child_tb_name);
		if (!is_array($child_db) || count($child_db) === 0) {
			$ctl->res_error_message("child_tb_name", $ctl->t("wizard.validation.child_note_not_found"));
			return;
		}
		$parent_db_id = $this->normalize_single_id($child_db["parent_tb_id"] ?? "");
		if ($parent_db_id === "") {
			$ctl->res_error_message("child_tb_name", $ctl->t("wizard.validation.child_note_parent_required"));
			return;
		}
		$parent_db = $ctl->db("db", "db")->get((int) $parent_db_id);
		if (!is_array($parent_db) || count($parent_db) === 0) {
			$ctl->res_error_message("child_tb_name", $ctl->t("wizard.validation.parent_note_not_found"));
			return;
		}
		$state = [
			"child_tb_name" => $child_tb_name,
			"child_db_id" => $this->normalize_single_id($child_db["id"] ?? ""),
			"child_menu_name" => trim((string) ($child_db["menu_name"] ?? "")),
			"parent_tb_name" => $this->normalize_table_name((string) ($parent_db["tb_name"] ?? "")),
			"parent_db_id" => $this->normalize_single_id($parent_db["id"] ?? ""),
			"parent_menu_name" => trim((string) ($parent_db["menu_name"] ?? "")),
			"dropdown_item" => trim((string) ($parent_db["dropdown_item"] ?? "id")),
			"dropdown_item_display_type" => trim((string) ($parent_db["dropdown_item_display_type"] ?? "field")),
			"dropdown_item_template" => trim((string) ($parent_db["dropdown_item_template"] ?? "")),
			"list_width" => (string) ((int) ($child_db["list_width"] ?? 800)),
			"cascade_delete_flag" => (string) ($child_db["cascade_delete_flag"] ?? "0")
		];
		$this->save_parent_child_note_state($ctl, $state);
		$this->show_parent_child_note_step_basic($ctl, $this->get_parent_child_note_state($ctl));
	}

	function submit_parent_child_note_basic_next(Controller $ctl) {
		$state = $this->get_parent_child_note_state($ctl);
		if ((string) ($state["child_tb_name"] ?? "") === "") {
			$ctl->show_notification_text($ctl->t("wizard.notification.reselect_child_note"), 3);
			return;
		}
		$dropdown_item_display_type = trim((string) $ctl->POST("dropdown_item_display_type"));
		if (!isset($this->get_dropdown_item_display_type_options()[$dropdown_item_display_type])) {
			$ctl->res_error_message("dropdown_item_display_type", $ctl->t("wizard.validation.parent_display_type_required"));
			return;
		}
		$dropdown_item = trim((string) $ctl->POST("dropdown_item"));
		$dropdown_item_opt = $this->get_parent_dropdown_item_options($ctl, (string) ($state["parent_tb_name"] ?? ""));
		if ($dropdown_item_display_type === "field" && !isset($dropdown_item_opt[$dropdown_item])) {
			$ctl->res_error_message("dropdown_item", $ctl->t("wizard.validation.parent_display_field_required"));
			return;
		}
		$dropdown_item_template = trim((string) $ctl->POST("dropdown_item_template"));
		if ($dropdown_item_display_type === "template" && $dropdown_item_template === "") {
			$ctl->res_error_message("dropdown_item_template", $ctl->t("wizard.validation.parent_display_template_required"));
			return;
		}
		$list_width = trim((string) $ctl->POST("list_width"));
		if (!$this->is_valid_parent_child_side_width($list_width)) {
			$ctl->res_error_message("list_width", $ctl->t("wizard.validation.child_side_width_required"));
			return;
		}
		$cascade_delete_flag = (string) $ctl->POST("cascade_delete_flag");
		if (!isset($this->get_cascade_delete_flag_options()[$cascade_delete_flag])) {
			$ctl->res_error_message("cascade_delete_flag", $ctl->t("wizard.validation.cascade_delete_required"));
			return;
		}
		$state["dropdown_item_display_type"] = $dropdown_item_display_type;
		$state["dropdown_item"] = $dropdown_item_display_type === "template" ? "id" : $dropdown_item;
		$state["dropdown_item_template"] = $dropdown_item_display_type === "template" ? $dropdown_item_template : "";
		$state["list_width"] = $list_width;
		$state["cascade_delete_flag"] = $cascade_delete_flag;
		$this->save_parent_child_note_state($ctl, $state);
		$this->show_parent_child_note_preview($ctl, $this->get_parent_child_note_state($ctl));
	}

	function submit_parent_child_note_exe(Controller $ctl) {
		$state = $this->get_parent_child_note_state($ctl);
		$post = $ctl->POST();
		if (!empty($post["child_tb_name"]) || !empty($post["parent_tb_name"])) {
			$state = array_merge($state, [
				"child_tb_name" => $this->normalize_table_name((string) ($post["child_tb_name"] ?? ($state["child_tb_name"] ?? ""))),
				"child_db_id" => $this->normalize_single_id($post["child_db_id"] ?? ($state["child_db_id"] ?? "")),
				"child_menu_name" => trim((string) ($post["child_menu_name"] ?? ($state["child_menu_name"] ?? ""))),
				"parent_tb_name" => $this->normalize_table_name((string) ($post["parent_tb_name"] ?? ($state["parent_tb_name"] ?? ""))),
				"parent_db_id" => $this->normalize_single_id($post["parent_db_id"] ?? ($state["parent_db_id"] ?? "")),
				"parent_menu_name" => trim((string) ($post["parent_menu_name"] ?? ($state["parent_menu_name"] ?? ""))),
				"dropdown_item" => trim((string) ($post["dropdown_item"] ?? ($state["dropdown_item"] ?? "id"))),
				"dropdown_item_display_type" => trim((string) ($post["dropdown_item_display_type"] ?? ($state["dropdown_item_display_type"] ?? "field"))),
				"dropdown_item_template" => trim((string) ($post["dropdown_item_template"] ?? ($state["dropdown_item_template"] ?? ""))),
				"list_width" => trim((string) ($post["list_width"] ?? ($state["list_width"] ?? "800"))),
				"cascade_delete_flag" => (string) ($post["cascade_delete_flag"] ?? ($state["cascade_delete_flag"] ?? "0"))
			]);
		}
		$parent_db_id = $this->normalize_single_id($state["parent_db_id"] ?? "");
		$child_db_id = $this->normalize_single_id($state["child_db_id"] ?? "");
		if ($parent_db_id === "" || $child_db_id === "") {
			$ctl->show_notification_text($ctl->t("wizard.notification.reselect_target_note"), 3);
			return;
		}
		$parent_db = $ctl->db("db", "db")->get((int) $parent_db_id);
		$child_db = $ctl->db("db", "db")->get((int) $child_db_id);
		if (!is_array($parent_db) || count($parent_db) === 0 || !is_array($child_db) || count($child_db) === 0) {
			$ctl->show_notification_text($ctl->t("wizard.validation.target_note_not_found"), 3);
			return;
		}
		$parent_db["dropdown_item"] = (string) ($state["dropdown_item"] ?? "id");
		$parent_db["dropdown_item_display_type"] = (string) ($state["dropdown_item_display_type"] ?? "field");
		$parent_db["dropdown_item_template"] = (string) ($state["dropdown_item_template"] ?? "");
		$child_db["list_width"] = (int) ($state["list_width"] ?? 800);
		$child_db["cascade_delete_flag"] = (int) ($state["cascade_delete_flag"] ?? 0);
		$ctl->db("db", "db")->update($parent_db);
		$ctl->db("db", "db")->update($child_db);
		$ctl->show_notification_text($ctl->t("wizard.notification.parent_child_saved"), 2);
		$this->reflesh_all_screen($ctl);
	}

	function back_to_parent_child_note_child(Controller $ctl) {
		$this->show_parent_child_note_step_child($ctl, $this->get_parent_child_note_state($ctl));
	}

	function back_to_parent_child_note_basic(Controller $ctl) {
		$this->show_parent_child_note_step_basic($ctl, $this->get_parent_child_note_state($ctl));
	}

	function open_table_create_wizard(Controller $ctl) {
		$state = [
			"project_name" => $this->detect_current_project_name(),
			"purpose" => "",
			"note_title" => "",
			"menu_name" => "",
			"field_mode" => "auto",
			"manual_fields_text" => ""
		];
		$this->save_table_create_state($ctl, $state);
		$this->show_step_purpose($ctl, $state);
	}

	function submit_purpose_next(Controller $ctl) {
		$purpose = trim((string) $ctl->POST("purpose"));
		if ($purpose === "") {
			$ctl->res_error_message("purpose", $ctl->t("wizard.validation.purpose_required"));
			return;
		}
		$state = $this->get_table_create_state($ctl);
		$state["purpose"] = $purpose;
		$this->save_table_create_state($ctl, $state);
		$this->show_step_table($ctl, $state);
	}

	function submit_table_next(Controller $ctl) {
		$menu_name = trim((string) $ctl->POST("menu_name"));
		if ($menu_name === "") {
			$ctl->res_error_message("menu_name", $ctl->t("wizard.validation.note_name_required"));
			return;
		}
		$state = $this->get_table_create_state($ctl);
		$state["note_title"] = $menu_name;
		$state["menu_name"] = $menu_name;
		$this->save_table_create_state($ctl, $state);
		$this->show_step_fields($ctl, $state);
	}

	function submit_fields_next(Controller $ctl) {
		$field_mode = trim((string) $ctl->POST("field_mode"));
		if ($field_mode !== "auto" && $field_mode !== "manual") {
			$ctl->res_error_message("field_mode", $ctl->t("wizard.validation.field_mode_required"));
			return;
		}

		$manual_fields_text = trim((string) $ctl->POST("manual_fields_text"));
		if ($field_mode === "manual") {
			if ($manual_fields_text === "") {
				$ctl->res_error_message("manual_fields_text", $ctl->t("wizard.validation.manual_fields_required"));
				return;
			}
			$duplicate_input = $this->find_duplicate_in_input_fields($manual_fields_text);
			if ($duplicate_input !== "") {
				$ctl->res_error_message("manual_fields_text", $ctl->t("wizard.validation.duplicate_fields", ["fields" => $duplicate_input]));
				return;
			}
		}

		$state = $this->get_table_create_state($ctl);
		$state["field_mode"] = $field_mode;
		$state["manual_fields_text"] = $field_mode === "manual" ? $manual_fields_text : "";
		$this->save_table_create_state($ctl, $state);
		$this->show_step_display($ctl, $this->get_table_create_state($ctl));
	}

	function submit_create_display_next(Controller $ctl) {
		$state = $this->get_table_create_state($ctl);
		$this->save_table_create_state($ctl, $state);
		$plan_lines = $this->build_table_create_plan_lines($state);
		$prompt_text = $this->build_table_create_prompt_text($state, $plan_lines);
		$ctl->set_session("wizard_table_create_prompt", $prompt_text);
		$ctl->set_session("wizard_current_prompt", $prompt_text);
		$ctl->assign("row", $state);
		$ctl->assign("plan_lines", $plan_lines);
		$ctl->assign("prompt_text", $prompt_text);
		$ctl->show_multi_dialog("wizard", "table_create_preview.tpl", $ctl->t("wizard.step_execute_confirm"), 980);
	}

	function back_to_purpose(Controller $ctl) {
		$state = $this->get_table_create_state($ctl);
		$this->show_step_purpose($ctl, $state);
	}

	function back_to_table(Controller $ctl) {
		$state = $this->get_table_create_state($ctl);
		$this->show_step_table($ctl, $state);
	}

	function back_to_fields(Controller $ctl) {
		$state = $this->get_table_create_state($ctl);
		$this->show_step_fields($ctl, $state);
	}

	function open_original_form_wizard(Controller $ctl) {
		$state = [
			"db_id" => "",
			"tb_name" => "",
			"parent_tb_id" => 0,
			"place" => "",
			"button_title" => "",
			"request_text" => ""
		];
		$this->save_original_form_state($ctl, $state);
		$this->show_original_form_step_table($ctl, $state);
	}

	function open_db_additionals_wizard(Controller $ctl) {
		$state = [
			"additional_type" => ""
		];
		$this->save_db_additionals_state($ctl, $state);
		$this->show_db_additionals_select($ctl, $state);
	}

	function submit_db_additionals_type_next(Controller $ctl) {
		$type = trim((string) $ctl->POST("additional_type"));
		if ($type === "") {
			$ctl->res_error_message("additional_type", $ctl->t("wizard.validation.additional_type_required"));
			return;
		}
		$state = $this->get_db_additionals_state($ctl);
		$state["additional_type"] = $type;
		$this->save_db_additionals_state($ctl, $state);
		if ($type === "original_form") {
			$this->open_original_form_wizard($ctl);
			return;
		}
		if ($type === "pdf") {
			$this->open_pdf_wizard($ctl);
			return;
		}
		if ($type === "csv_download") {
			$this->open_csv_download_wizard($ctl);
			return;
		}
		if ($type === "csv_upload") {
			$this->open_csv_upload_wizard($ctl);
			return;
		}
		if ($type === "chart") {
			$this->open_chart_wizard($ctl);
			return;
		}
		if ($type === "line_message") {
			$this->open_line_message_wizard($ctl);
			return;
		}
		if ($type !== "original_form") {
			$ctl->show_notification_text($ctl->t("wizard.notification.db_additionals_preparing"), 3);
			return;
		}
	}

	function open_line_message_wizard(Controller $ctl) {
		$state = [
			"db_id" => "",
			"tb_name" => "",
			"parent_tb_id" => 0,
			"place" => "",
			"button_title" => "Lineテキスト送信",
			"request_text" => ""
		];
		$this->save_line_message_state($ctl, $state);
		$this->show_line_message_step_table($ctl, $state);
	}

	function submit_line_message_table_next(Controller $ctl) {
		$db_id = $this->normalize_single_id((string) $ctl->POST("db_id"));
		if ($db_id === "") {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.button_table_required"));
			return;
		}
		$db = $ctl->db("db", "db")->get($db_id);
		if (!is_array($db) || count($db) === 0) {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.target_table_not_found"));
			return;
		}
		$tb_name = $this->normalize_table_name((string) ($db["tb_name"] ?? ""));
		if ($tb_name === "") {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.invalid_target_tb_name"));
			return;
		}
		$state = $this->get_line_message_state($ctl);
		$state["db_id"] = $db_id;
		$state["tb_name"] = $tb_name;
		$state["parent_tb_id"] = (int) ($db["parent_tb_id"] ?? 0);
		$place_opt = $this->get_original_form_place_options($state["parent_tb_id"] > 0);
		if (!isset($place_opt[$state["place"]])) {
			$state["place"] = (string) array_key_first($place_opt);
		}
		$this->save_line_message_state($ctl, $state);
		$this->show_line_message_step_place($ctl, $state);
	}

	function submit_line_message_place_next(Controller $ctl) {
		$state = $this->get_line_message_state($ctl);
		$place = (string) $ctl->POST("place");
		$place_opt = $this->get_original_form_place_options(((int) ($state["parent_tb_id"] ?? 0)) > 0);
		if (!isset($place_opt[$place])) {
			$ctl->res_error_message("place", $ctl->t("wizard.validation.place_required"));
			return;
		}
		$state["place"] = $place;
		$default_request_text = $this->build_line_message_default_request_text($this->resolve_line_message_send_mode($place));
		if ($state["request_text"] === "" || $this->is_line_message_default_request_text((string) $state["request_text"])) {
			$state["request_text"] = $default_request_text;
		}
		$this->save_line_message_state($ctl, $state);
		$this->show_line_message_step_request($ctl, $state);
	}

	function submit_line_message_request_next(Controller $ctl) {
		$button_title = trim((string) $ctl->POST("button_title"));
		if ($button_title === "") {
			$ctl->res_error_message("button_title", $ctl->t("wizard.validation.button_title_required"));
			return;
		}
		$request_text = trim((string) $ctl->POST("request_text"));
		if ($request_text === "") {
			$ctl->res_error_message("request_text", $ctl->t("wizard.validation.request_text_required"));
			return;
		}
		$state = $this->get_line_message_state($ctl);
		$state["button_title"] = $button_title;
		$state["request_text"] = $request_text;
		$this->save_line_message_state($ctl, $state);

		$prompt = $this->build_line_message_prompt_text($state);
		$ctl->set_session("wizard_current_prompt", "");
		$ctl->set_session("wizard_table_create_prompt", "");
		$ctl->set_session("wizard_table_change_prompt", "");
		$ctl->set_session("codex_terminal_initial_input", $prompt);
		$ctl->close_multi_dialog("wizard");
		$ctl->invoke("run", [], "codex_terminal");
	}

	function back_to_line_message_table(Controller $ctl) {
		$state = $this->get_line_message_state($ctl);
		$this->show_line_message_step_table($ctl, $state);
	}

	function back_to_line_message_place(Controller $ctl) {
		$state = $this->get_line_message_state($ctl);
		$this->show_line_message_step_place($ctl, $state);
	}

	function open_pdf_wizard(Controller $ctl) {
		$state = [
			"db_id" => "",
			"tb_name" => "",
			"parent_tb_id" => 0,
			"place" => "",
			"field_ids" => [],
			"button_title" => "",
			"request_text" => ""
		];
		$this->save_pdf_state($ctl, $state);
		$this->show_pdf_step_table($ctl, $state);
	}

	function submit_pdf_table_next(Controller $ctl) {
		$db_id = (string) $ctl->POST("db_id");
		$db_id = $this->normalize_single_id($db_id);
		if ($db_id === "") {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.button_table_required"));
			return;
		}
		$db = $ctl->db("db", "db")->get($db_id);
		if (!is_array($db) || count($db) === 0) {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.target_table_not_found"));
			return;
		}
		$tb_name = $this->normalize_table_name((string) ($db["tb_name"] ?? ""));
		if ($tb_name === "") {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.invalid_target_tb_name"));
			return;
		}
		$state = $this->get_pdf_state($ctl);
		$state["db_id"] = $db_id;
		$state["tb_name"] = $tb_name;
		$state["parent_tb_id"] = (int) ($db["parent_tb_id"] ?? 0);
		$state["field_ids"] = [];
		$place_opt = $this->get_original_form_place_options($state["parent_tb_id"] > 0);
		if (!isset($place_opt[$state["place"]])) {
			$state["place"] = (string) array_key_first($place_opt);
		}
		$this->save_pdf_state($ctl, $state);
		$this->show_pdf_step_place($ctl, $state);
	}

	function submit_pdf_place_next(Controller $ctl) {
		$state = $this->get_pdf_state($ctl);
		$place = (string) $ctl->POST("place");
		$place_opt = $this->get_original_form_place_options(((int) ($state["parent_tb_id"] ?? 0)) > 0);
		if (!isset($place_opt[$place])) {
			$ctl->res_error_message("place", $ctl->t("wizard.validation.place_required"));
			return;
		}
		$state["place"] = $place;
		$this->save_pdf_state($ctl, $state);
		$this->show_pdf_step_fields($ctl, $state);
	}

	function submit_pdf_fields_next(Controller $ctl) {
		$state = $this->get_pdf_state($ctl);
		$field_ids = $this->normalize_id_list($ctl->POST("field_ids"));
		if (count($field_ids) === 0) {
			$ctl->res_error_message("field_ids", $ctl->t("wizard.validation.use_fields_required"));
			return;
		}
		$valid_rows = $this->get_table_field_detail_rows($ctl, (string) ($state["tb_name"] ?? ""));
		$valid_map = [];
		foreach ($valid_rows as $row) {
			$id = (string) ($row["id"] ?? "");
			if ($id !== "") {
				$valid_map[$id] = 1;
			}
		}
		$selected = [];
		foreach ($field_ids as $id) {
			if (!isset($valid_map[$id])) {
				continue;
			}
			$selected[] = $id;
		}
		if (count($selected) === 0) {
			$ctl->res_error_message("field_ids", $ctl->t("wizard.validation.selected_fields_not_found"));
			return;
		}
		$state["field_ids"] = $selected;
		$this->save_pdf_state($ctl, $state);
		$this->show_pdf_step_request($ctl, $state);
	}

	function submit_pdf_request_next(Controller $ctl) {
		$button_title = trim((string) $ctl->POST("button_title"));
		if ($button_title === "") {
			$ctl->res_error_message("button_title", $ctl->t("wizard.validation.button_title_required"));
			return;
		}
		$request_text = trim((string) $ctl->POST("request_text"));
		if ($request_text === "") {
			$ctl->res_error_message("request_text", $ctl->t("wizard.validation.request_text_required"));
			return;
		}
		$state = $this->get_pdf_state($ctl);
		$state["button_title"] = $button_title;
		$state["request_text"] = $request_text;
		$this->save_pdf_state($ctl, $state);

		$prompt = $this->build_pdf_prompt_text($ctl, $state);
		$ctl->set_session("wizard_current_prompt", "");
		$ctl->set_session("wizard_table_create_prompt", "");
		$ctl->set_session("wizard_table_change_prompt", "");
		$ctl->set_session("codex_terminal_initial_input", $prompt);
		$ctl->close_multi_dialog("wizard");
		$ctl->invoke("run", [], "codex_terminal");
	}

	function back_to_pdf_table(Controller $ctl) {
		$state = $this->get_pdf_state($ctl);
		$this->show_pdf_step_table($ctl, $state);
	}

	function back_to_pdf_place(Controller $ctl) {
		$state = $this->get_pdf_state($ctl);
		$this->show_pdf_step_place($ctl, $state);
	}

	function back_to_pdf_fields(Controller $ctl) {
		$state = $this->get_pdf_state($ctl);
		$this->show_pdf_step_fields($ctl, $state);
	}

	function open_csv_download_wizard(Controller $ctl) {
		$state = [
			"db_id" => "",
			"tb_name" => "",
			"parent_tb_id" => 0,
			"place" => "",
			"field_ids" => [],
			"button_title" => "",
			"request_text" => ""
		];
		$this->save_csv_download_state($ctl, $state);
		$this->show_csv_download_step_table($ctl, $state);
	}

	function submit_csv_download_table_next(Controller $ctl) {
		$db_id = (string) $ctl->POST("db_id");
		$db_id = $this->normalize_single_id($db_id);
		if ($db_id === "") {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.button_table_required"));
			return;
		}
		$db = $ctl->db("db", "db")->get($db_id);
		if (!is_array($db) || count($db) === 0) {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.target_table_not_found"));
			return;
		}
		$tb_name = $this->normalize_table_name((string) ($db["tb_name"] ?? ""));
		if ($tb_name === "") {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.invalid_target_tb_name"));
			return;
		}
		$state = $this->get_csv_download_state($ctl);
		$state["db_id"] = $db_id;
		$state["tb_name"] = $tb_name;
		$state["parent_tb_id"] = (int) ($db["parent_tb_id"] ?? 0);
		$state["field_ids"] = [];
		$place_opt = $this->get_original_form_place_options($state["parent_tb_id"] > 0);
		if (!isset($place_opt[$state["place"]])) {
			$state["place"] = (string) array_key_first($place_opt);
		}
		$this->save_csv_download_state($ctl, $state);
		$this->show_csv_download_step_place($ctl, $state);
	}

	function submit_csv_download_place_next(Controller $ctl) {
		$state = $this->get_csv_download_state($ctl);
		$place = (string) $ctl->POST("place");
		$place_opt = $this->get_original_form_place_options(((int) ($state["parent_tb_id"] ?? 0)) > 0);
		if (!isset($place_opt[$place])) {
			$ctl->res_error_message("place", $ctl->t("wizard.validation.place_required"));
			return;
		}
		$state["place"] = $place;
		$this->save_csv_download_state($ctl, $state);
		$this->show_csv_download_step_fields($ctl, $state);
	}

	function submit_csv_download_fields_next(Controller $ctl) {
		$state = $this->get_csv_download_state($ctl);
		$field_ids = $this->normalize_id_list($ctl->POST("field_ids"));
		if (count($field_ids) === 0) {
			$ctl->res_error_message("field_ids", $ctl->t("wizard.validation.use_fields_required"));
			return;
		}
		$valid_rows = $this->get_table_field_detail_rows($ctl, (string) ($state["tb_name"] ?? ""));
		$valid_map = [];
		foreach ($valid_rows as $row) {
			$id = (string) ($row["id"] ?? "");
			if ($id !== "") {
				$valid_map[$id] = 1;
			}
		}
		$selected = [];
		foreach ($field_ids as $id) {
			if (!isset($valid_map[$id])) {
				continue;
			}
			$selected[] = $id;
		}
		if (count($selected) === 0) {
			$ctl->res_error_message("field_ids", $ctl->t("wizard.validation.selected_fields_not_found"));
			return;
		}
		$state["field_ids"] = $selected;
		$this->save_csv_download_state($ctl, $state);
		$this->show_csv_download_step_request($ctl, $state);
	}

	function submit_csv_download_request_next(Controller $ctl) {
		$button_title = trim((string) $ctl->POST("button_title"));
		if ($button_title === "") {
			$ctl->res_error_message("button_title", $ctl->t("wizard.validation.button_title_required"));
			return;
		}
		$request_text = trim((string) $ctl->POST("request_text"));
		if ($request_text === "") {
			$ctl->res_error_message("request_text", $ctl->t("wizard.validation.request_text_required"));
			return;
		}
		$state = $this->get_csv_download_state($ctl);
		$state["button_title"] = $button_title;
		$state["request_text"] = $request_text;
		$this->save_csv_download_state($ctl, $state);

		$prompt = $this->build_csv_download_prompt_text($ctl, $state);
		$ctl->set_session("wizard_current_prompt", "");
		$ctl->set_session("wizard_table_create_prompt", "");
		$ctl->set_session("wizard_table_change_prompt", "");
		$ctl->set_session("codex_terminal_initial_input", $prompt);
		$ctl->close_multi_dialog("wizard");
		$ctl->invoke("run", [], "codex_terminal");
	}

	function back_to_csv_download_table(Controller $ctl) {
		$state = $this->get_csv_download_state($ctl);
		$this->show_csv_download_step_table($ctl, $state);
	}

	function back_to_csv_download_place(Controller $ctl) {
		$state = $this->get_csv_download_state($ctl);
		$this->show_csv_download_step_place($ctl, $state);
	}

	function back_to_csv_download_fields(Controller $ctl) {
		$state = $this->get_csv_download_state($ctl);
		$this->show_csv_download_step_fields($ctl, $state);
	}

	function open_csv_upload_wizard(Controller $ctl) {
		$state = [
			"db_id" => "",
			"tb_name" => "",
			"parent_tb_id" => 0,
			"place" => "",
			"field_ids" => [],
			"button_title" => "",
			"request_text" => ""
		];
		$this->save_csv_upload_state($ctl, $state);
		$this->show_csv_upload_step_table($ctl, $state);
	}

	function submit_csv_upload_table_next(Controller $ctl) {
		$db_id = (string) $ctl->POST("db_id");
		$db_id = $this->normalize_single_id($db_id);
		if ($db_id === "") {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.button_table_required"));
			return;
		}
		$db = $ctl->db("db", "db")->get($db_id);
		if (!is_array($db) || count($db) === 0) {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.target_table_not_found"));
			return;
		}
		$tb_name = $this->normalize_table_name((string) ($db["tb_name"] ?? ""));
		if ($tb_name === "") {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.invalid_target_tb_name"));
			return;
		}
		$state = $this->get_csv_upload_state($ctl);
		$state["db_id"] = $db_id;
		$state["tb_name"] = $tb_name;
		$state["parent_tb_id"] = (int) ($db["parent_tb_id"] ?? 0);
		$state["field_ids"] = [];
		$place_opt = $this->get_original_form_place_options($state["parent_tb_id"] > 0);
		if (!isset($place_opt[$state["place"]])) {
			$state["place"] = (string) array_key_first($place_opt);
		}
		$this->save_csv_upload_state($ctl, $state);
		$this->show_csv_upload_step_place($ctl, $state);
	}

	function submit_csv_upload_place_next(Controller $ctl) {
		$state = $this->get_csv_upload_state($ctl);
		$place = (string) $ctl->POST("place");
		$place_opt = $this->get_original_form_place_options(((int) ($state["parent_tb_id"] ?? 0)) > 0);
		if (!isset($place_opt[$place])) {
			$ctl->res_error_message("place", $ctl->t("wizard.validation.place_required"));
			return;
		}
		$state["place"] = $place;
		$this->save_csv_upload_state($ctl, $state);
		$this->show_csv_upload_step_fields($ctl, $state);
	}

	function submit_csv_upload_fields_next(Controller $ctl) {
		$state = $this->get_csv_upload_state($ctl);
		$field_ids = $this->normalize_id_list($ctl->POST("field_ids"));
		if (count($field_ids) === 0) {
			$ctl->res_error_message("field_ids", $ctl->t("wizard.validation.use_fields_required"));
			return;
		}
		$valid_rows = $this->get_table_field_detail_rows($ctl, (string) ($state["tb_name"] ?? ""));
		$valid_map = [];
		foreach ($valid_rows as $row) {
			$id = (string) ($row["id"] ?? "");
			if ($id !== "") {
				$valid_map[$id] = 1;
			}
		}
		$selected = [];
		foreach ($field_ids as $id) {
			if (!isset($valid_map[$id])) {
				continue;
			}
			$selected[] = $id;
		}
		if (count($selected) === 0) {
			$ctl->res_error_message("field_ids", $ctl->t("wizard.validation.selected_fields_not_found"));
			return;
		}
		$state["field_ids"] = $selected;
		$this->save_csv_upload_state($ctl, $state);
		$this->show_csv_upload_step_request($ctl, $state);
	}

	function submit_csv_upload_request_next(Controller $ctl) {
		$button_title = trim((string) $ctl->POST("button_title"));
		if ($button_title === "") {
			$ctl->res_error_message("button_title", $ctl->t("wizard.validation.button_title_required"));
			return;
		}
		$request_text = trim((string) $ctl->POST("request_text"));
		if ($request_text === "") {
			$ctl->res_error_message("request_text", $ctl->t("wizard.validation.request_text_required"));
			return;
		}
		$state = $this->get_csv_upload_state($ctl);
		$state["button_title"] = $button_title;
		$state["request_text"] = $request_text;
		$this->save_csv_upload_state($ctl, $state);

		$prompt = $this->build_csv_upload_prompt_text($ctl, $state);
		$ctl->set_session("wizard_current_prompt", "");
		$ctl->set_session("wizard_table_create_prompt", "");
		$ctl->set_session("wizard_table_change_prompt", "");
		$ctl->set_session("codex_terminal_initial_input", $prompt);
		$ctl->close_multi_dialog("wizard");
		$ctl->invoke("run", [], "codex_terminal");
	}

	function back_to_csv_upload_table(Controller $ctl) {
		$state = $this->get_csv_upload_state($ctl);
		$this->show_csv_upload_step_table($ctl, $state);
	}

	function back_to_csv_upload_place(Controller $ctl) {
		$state = $this->get_csv_upload_state($ctl);
		$this->show_csv_upload_step_place($ctl, $state);
	}

	function back_to_csv_upload_fields(Controller $ctl) {
		$state = $this->get_csv_upload_state($ctl);
		$this->show_csv_upload_step_fields($ctl, $state);
	}

	function open_chart_wizard(Controller $ctl) {
		$state = [
			"db_id" => "",
			"tb_name" => "",
			"parent_tb_id" => 0,
			"place" => "",
			"chart_type" => "",
			"field_ids" => [],
			"button_title" => "",
			"request_text" => ""
		];
		$this->save_chart_state($ctl, $state);
		$this->show_chart_step_table($ctl, $state);
	}

	function submit_chart_table_next(Controller $ctl) {
		$db_id = (string) $ctl->POST("db_id");
		$db_id = $this->normalize_single_id($db_id);
		if ($db_id === "") {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.button_table_required"));
			return;
		}
		$db = $ctl->db("db", "db")->get($db_id);
		if (!is_array($db) || count($db) === 0) {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.target_table_not_found"));
			return;
		}
		$tb_name = $this->normalize_table_name((string) ($db["tb_name"] ?? ""));
		if ($tb_name === "") {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.invalid_target_tb_name"));
			return;
		}
		$state = $this->get_chart_state($ctl);
		$state["db_id"] = $db_id;
		$state["tb_name"] = $tb_name;
		$state["parent_tb_id"] = (int) ($db["parent_tb_id"] ?? 0);
		$state["field_ids"] = [];
		$place_opt = $this->get_original_form_place_options($state["parent_tb_id"] > 0);
		if (!isset($place_opt[$state["place"]])) {
			$state["place"] = (string) array_key_first($place_opt);
		}
		if ($state["chart_type"] === "") {
			$chart_opt = $this->get_chart_type_options();
			$state["chart_type"] = (string) array_key_first($chart_opt);
		}
		$this->save_chart_state($ctl, $state);
		$this->show_chart_step_place($ctl, $state);
	}

	function submit_chart_place_next(Controller $ctl) {
		$state = $this->get_chart_state($ctl);
		$place = (string) $ctl->POST("place");
		$place_opt = $this->get_original_form_place_options(((int) ($state["parent_tb_id"] ?? 0)) > 0);
		if (!isset($place_opt[$place])) {
			$ctl->res_error_message("place", $ctl->t("wizard.validation.place_required"));
			return;
		}
		$state["place"] = $place;
		$this->save_chart_state($ctl, $state);
		$this->show_chart_step_type($ctl, $state);
	}

	function submit_chart_type_next(Controller $ctl) {
		$state = $this->get_chart_state($ctl);
		$chart_type = trim((string) $ctl->POST("chart_type"));
		$chart_opt = $this->get_chart_type_options();
		if (!isset($chart_opt[$chart_type])) {
			$ctl->res_error_message("chart_type", $ctl->t("wizard.validation.chart_type_required"));
			return;
		}
		$state["chart_type"] = $chart_type;
		$this->save_chart_state($ctl, $state);
		$this->show_chart_step_fields($ctl, $state);
	}

	function submit_chart_fields_next(Controller $ctl) {
		$state = $this->get_chart_state($ctl);
		$field_ids = $this->normalize_id_list($ctl->POST("field_ids"));
		if (count($field_ids) === 0) {
			$ctl->res_error_message("field_ids", $ctl->t("wizard.validation.chart_fields_required"));
			return;
		}
		$valid_rows = $this->get_table_field_detail_rows($ctl, (string) ($state["tb_name"] ?? ""));
		$valid_map = [];
		foreach ($valid_rows as $row) {
			$id = (string) ($row["id"] ?? "");
			if ($id !== "") {
				$valid_map[$id] = 1;
			}
		}
		$selected = [];
		foreach ($field_ids as $id) {
			if (!isset($valid_map[$id])) {
				continue;
			}
			$selected[] = $id;
		}
		if (count($selected) === 0) {
			$ctl->res_error_message("field_ids", $ctl->t("wizard.validation.selected_fields_not_found"));
			return;
		}
		$state["field_ids"] = $selected;
		$this->save_chart_state($ctl, $state);
		$this->show_chart_step_request($ctl, $state);
	}

	function submit_chart_request_next(Controller $ctl) {
		$button_title = trim((string) $ctl->POST("button_title"));
		if ($button_title === "") {
			$ctl->res_error_message("button_title", $ctl->t("wizard.validation.button_title_required"));
			return;
		}
		$request_text = trim((string) $ctl->POST("request_text"));
		if ($request_text === "") {
			$ctl->res_error_message("request_text", $ctl->t("wizard.validation.request_text_required"));
			return;
		}
		$state = $this->get_chart_state($ctl);
		$state["button_title"] = $button_title;
		$state["request_text"] = $request_text;
		$this->save_chart_state($ctl, $state);

		$prompt = $this->build_chart_prompt_text($ctl, $state);
		$ctl->set_session("wizard_current_prompt", "");
		$ctl->set_session("wizard_table_create_prompt", "");
		$ctl->set_session("wizard_table_change_prompt", "");
		$ctl->set_session("codex_terminal_initial_input", $prompt);
		$ctl->close_multi_dialog("wizard");
		$ctl->invoke("run", [], "codex_terminal");
	}

	function back_to_chart_table(Controller $ctl) {
		$state = $this->get_chart_state($ctl);
		$this->show_chart_step_table($ctl, $state);
	}

	function back_to_chart_place(Controller $ctl) {
		$state = $this->get_chart_state($ctl);
		$this->show_chart_step_place($ctl, $state);
	}

	function back_to_chart_type(Controller $ctl) {
		$state = $this->get_chart_state($ctl);
		$this->show_chart_step_type($ctl, $state);
	}

	function back_to_chart_fields(Controller $ctl) {
		$state = $this->get_chart_state($ctl);
		$this->show_chart_step_fields($ctl, $state);
	}

	function open_line_bot_wizard(Controller $ctl) {
		$state = [
			"line_action" => "",
			"event_type" => "",
			"keyword" => "",
			"button_title" => "",
			"request_text" => "",
			"line_channel_secret" => "",
			"line_accesstoken" => "",
			"line_channel_secret_saved" => "0",
			"line_accesstoken_saved" => "0"
		];
		$this->save_line_bot_state($ctl, $state);
		$this->show_line_bot_step_select($ctl, $state);
	}

	function open_cron_wizard(Controller $ctl) {
		$state = [
			"cron_action" => "",
			"cron_id" => "",
			"timing_text" => "",
			"request_text" => "",
			"title" => "",
			"class_name" => "",
			"function_name" => "",
			"min" => [],
			"hour" => [],
			"day" => [],
			"month" => [],
			"weekday" => [],
			"summary" => ""
		];
		$this->save_cron_state($ctl, $state);
		$this->show_cron_step_select($ctl, $state);
	}

	function submit_cron_action_next(Controller $ctl) {
		$action = trim((string) $ctl->POST("cron_action"));
		if ($action === "") {
			$ctl->res_error_message("cron_action", $ctl->t("wizard.validation.choose_action"));
			return;
		}
		$state = $this->get_cron_state($ctl);
		$state["cron_action"] = $action;
		$this->save_cron_state($ctl, $state);
		if ($action === "add") {
			$this->show_cron_step_timing($ctl, $state);
			return;
		}
		if ($action === "edit") {
			$this->show_cron_step_target($ctl, $state);
			return;
		}
		if ($action === "delete") {
			$this->show_cron_step_target($ctl, $state);
			return;
		}
		if ($action === "start") {
			$this->show_cron_step_start($ctl, $state);
			return;
		}
		$ctl->show_notification_text($ctl->t("wizard.notification.cron_preparing"), 3);
	}

	function submit_cron_target_next(Controller $ctl) {
		$cron_id = $this->normalize_single_id((string) $ctl->POST("cron_id"));
		if ($cron_id === "") {
			$action = (string) ($this->get_cron_state($ctl)["cron_action"] ?? "");
			$ctl->res_error_message("cron_id", $action === "delete" ? $ctl->t("wizard.validation.cron_delete_target_required") : $ctl->t("wizard.validation.cron_edit_target_required"));
			return;
		}
		$data = $this->get_cron_editable_row($ctl, $cron_id);
		if ($data === null) {
			$action = (string) ($this->get_cron_state($ctl)["cron_action"] ?? "");
			$ctl->res_error_message("cron_id", $action === "delete" ? $ctl->t("wizard.validation.cron_delete_target_not_found") : $ctl->t("wizard.validation.cron_edit_target_not_found"));
			return;
		}
		$state = $this->get_cron_state($ctl);
		if ((string) ($state["cron_action"] ?? "") === "delete") {
			$deleted = $ctl->db("cron", "cron")->delete((int) $cron_id);
			if ((int) $deleted === 0) {
				$ctl->res_error_message("cron_id", $ctl->t("wizard.validation.cron_delete_target_not_found"));
				return;
			}
			$ctl->cron_set();
			$this->save_cron_state($ctl, []);
			$this->reflesh_all_screen($ctl);
			return;
		}
		$state["cron_id"] = $cron_id;
		$state["title"] = trim((string) ($data["title"] ?? ""));
		$state["class_name"] = trim((string) ($data["class_name"] ?? ""));
		$state["function_name"] = trim((string) ($data["function_name"] ?? ""));
		$state["min"] = $this->normalize_cron_component_list($data["min"] ?? null);
		$state["hour"] = $this->normalize_cron_component_list($data["hour"] ?? null);
		$state["day"] = $this->normalize_cron_component_list($data["day"] ?? null);
		$state["month"] = $this->normalize_cron_component_list($data["month"] ?? null);
		$state["weekday"] = $this->normalize_cron_component_list($data["weekday"] ?? null);
		$state["timing_text"] = $this->build_cron_human_timing_text($state);
		if ($state["request_text"] === "") {
			$state["request_text"] = $state["title"];
		}
		$this->save_cron_state($ctl, $state);
		$this->show_cron_step_timing($ctl, $state);
	}

	function submit_cron_start_exe(Controller $ctl) {
		$ctl->cron_set();
		$this->save_cron_state($ctl, []);
		$this->reflesh_all_screen($ctl);
	}

	function submit_cron_timing_next(Controller $ctl) {
		$timing_text = trim((string) $ctl->POST("timing_text"));
		if ($timing_text === "") {
			$ctl->res_error_message("timing_text", $ctl->t("wizard.validation.timing_text_required"));
			return;
		}
		$request_text = trim((string) $ctl->POST("request_text"));
		if ($request_text === "") {
			$ctl->res_error_message("request_text", $ctl->t("wizard.validation.cron_request_required"));
			return;
		}
		$state = $this->get_cron_state($ctl);
		$state["timing_text"] = $timing_text;
		$state["request_text"] = $request_text;
		$state["summary"] = $request_text;
		$this->save_cron_state($ctl, $state);
		$prompt = ((string) ($state["cron_action"] ?? "")) === "edit"
			? $this->build_cron_edit_prompt_text($state)
			: $this->build_cron_add_prompt_text($state);
		$ctl->set_session("wizard_current_prompt", "");
		$ctl->set_session("wizard_table_create_prompt", "");
		$ctl->set_session("wizard_table_change_prompt", "");
		$ctl->set_session("codex_terminal_initial_input", $prompt);
		$ctl->close_multi_dialog("wizard");
		$ctl->invoke("run", [], "codex_terminal");
	}

	function back_to_cron_select(Controller $ctl) {
		$state = $this->get_cron_state($ctl);
		$this->show_cron_step_select($ctl, $state);
	}

	function back_to_cron_target(Controller $ctl) {
		$state = $this->get_cron_state($ctl);
		$this->show_cron_step_target($ctl, $state);
	}

	function open_public_pages_wizard(Controller $ctl) {
		// Memo: public_pages wizard is currently hidden from wizard/run and kept for future re-enable.
		$state = [
			"page_action" => "",
			"registry_id" => "",
			"title" => "",
			"function_name" => "",
			"template_name" => "",
			"public_asset_ids" => [],
			"public_asset_text" => "",
			"request_text" => "",
			"common_header_text" => "",
			"common_footer_text" => "",
			"common_nav_text" => "",
			"common_style_text" => ""
		];
		$this->save_public_pages_state($ctl, $state);
		$this->show_public_pages_step_select($ctl, $state);
	}

	function submit_public_pages_action_next(Controller $ctl) {
		$action = trim((string) $ctl->POST("page_action"));
		if ($action === "") {
			$ctl->res_error_message("page_action", $ctl->t("wizard.validation.choose_action"));
			return;
		}
		$state = $this->get_public_pages_state($ctl);
		$state["page_action"] = $action;
		$this->save_public_pages_state($ctl, $state);
		if ($action === "asset_add") {
			$this->show_public_pages_step_asset_add($ctl, $state);
			return;
		}
		if ($action === "common_design") {
			$this->show_public_pages_step_assets($ctl, $state);
			return;
		}
		if ($action === "menu_manage") {
			$this->show_public_pages_step_menu_manage($ctl, $state);
			return;
		}
		if ($action === "add") {
			$this->show_public_pages_step_add_info($ctl, $state);
			return;
		}
		if ($action === "edit" || $action === "delete") {
			$this->show_public_pages_step_target($ctl, $state);
			return;
		}
		$ctl->show_notification_text($ctl->t("wizard.notification.preparing"), 3);
	}

	function submit_public_pages_add_info_next(Controller $ctl) {
		$title = trim((string) $ctl->POST("title"));
		if ($title === "") {
			$ctl->res_error_message("title", $ctl->t("wizard.validation.page_title_required"));
			return;
		}
		$function_name = $this->suggest_unique_public_function_name($ctl, $title);
		if ($function_name === "") {
			$ctl->res_error_message("title", $ctl->t("wizard.validation.function_name_auto_failed"));
			return;
		}
		$state = $this->get_public_pages_state($ctl);
		$state["title"] = $title;
		$state["function_name"] = $function_name;
		$state["template_name"] = $this->build_public_pages_template_name($function_name);
		$this->save_public_pages_state($ctl, $state);
		$this->show_public_pages_step_assets($ctl, $state);
	}

	function submit_public_pages_target_next(Controller $ctl) {
		$registry_id = $this->normalize_single_id((string) $ctl->POST("registry_id"));
		if ($registry_id === "") {
			$action = (string) ($this->get_public_pages_state($ctl)["page_action"] ?? "");
			$ctl->res_error_message("registry_id", $action === "delete" ? $ctl->t("wizard.validation.page_delete_target_required") : $ctl->t("wizard.validation.page_edit_target_required"));
			return;
		}
		$row = $this->get_public_pages_registry_row($ctl, $registry_id);
		if ($row === null) {
			$ctl->res_error_message("registry_id", $ctl->t("wizard.validation.page_target_not_found"));
			return;
		}
		$state = $this->get_public_pages_state($ctl);
		$state["registry_id"] = $registry_id;
		$state["title"] = trim((string) ($row["title"] ?? ""));
		$state["function_name"] = trim((string) ($row["function_name"] ?? ""));
		$state["template_name"] = trim((string) ($row["template_name"] ?? ""));
		$this->save_public_pages_state($ctl, $state);
		if ((string) ($state["page_action"] ?? "") === "delete") {
			$ctl->db("public_pages_registry", "public_pages_registry")->delete((int) $registry_id);
			$this->save_public_pages_state($ctl, []);
			$this->reflesh_all_screen($ctl);
			return;
		}
		$this->show_public_pages_step_assets($ctl, $state);
	}

	function submit_public_pages_assets_next(Controller $ctl) {
		$state = $this->get_public_pages_state($ctl);
		if ((string) ($state["page_action"] ?? "") === "") {
			$state["page_action"] = trim((string) $ctl->POST("page_action"));
		}
		$public_asset_ids = $this->normalize_id_list($ctl->POST("public_asset_ids"));
		$state["public_asset_ids"] = $public_asset_ids;
		$state["public_asset_text"] = $this->build_public_asset_prompt_text($ctl, $public_asset_ids);
		$this->save_public_pages_state($ctl, $state);
		if ((string) ($state["page_action"] ?? "") === "common_design") {
			$this->show_public_pages_step_common_header($ctl, $state);
			return;
		}
		$this->show_public_pages_step_request($ctl, $state);
	}

	function submit_public_pages_request_next(Controller $ctl) {
		$state = $this->get_public_pages_state($ctl);
		$action = (string) ($state["page_action"] ?? "");
		$request_text = trim((string) $ctl->POST("request_text"));
		if ($request_text === "") {
			$ctl->res_error_message("request_text", $ctl->t("wizard.validation.request_text_required"));
			return;
		}
		$state["request_text"] = $request_text;
		$this->save_public_pages_state($ctl, $state);
		if ($action === "common_design") {
			$prompt = $this->build_public_pages_common_design_prompt_text($state);
		} elseif ($action === "edit") {
			$prompt = $this->build_public_pages_edit_prompt_text($state);
		} else {
			$prompt = $this->build_public_pages_add_prompt_text($state);
		}
		$ctl->set_session("wizard_current_prompt", $prompt);
		$this->open_codex_terminal_with_prompt($ctl);
	}

	function submit_public_pages_common_header_next(Controller $ctl) {
		$state = $this->get_public_pages_state($ctl);
		$state["common_header_text"] = trim((string) $ctl->POST("step_value"));
		$this->save_public_pages_state($ctl, $state);
		$this->show_public_pages_step_common_footer($ctl, $state);
	}

	function submit_public_pages_common_footer_next(Controller $ctl) {
		$state = $this->get_public_pages_state($ctl);
		$state["common_footer_text"] = trim((string) $ctl->POST("step_value"));
		$this->save_public_pages_state($ctl, $state);
		$this->show_public_pages_step_common_nav($ctl, $state);
	}

	function submit_public_pages_common_nav_next(Controller $ctl) {
		$state = $this->get_public_pages_state($ctl);
		$state["common_nav_text"] = trim((string) $ctl->POST("step_value"));
		$this->save_public_pages_state($ctl, $state);
		$this->show_public_pages_step_common_style($ctl, $state);
	}

	function submit_public_pages_common_style_next(Controller $ctl) {
		$state = $this->get_public_pages_state($ctl);
		$state["common_style_text"] = trim((string) $ctl->POST("step_value"));
		$state["request_text"] = $this->build_public_pages_common_design_request_text($state);
		$this->save_public_pages_state($ctl, $state);
		$this->show_public_pages_step_common_confirm($ctl, $state);
	}

	function submit_public_pages_common_confirm_next(Controller $ctl) {
		$state = $this->get_public_pages_state($ctl);
		$state["request_text"] = $this->build_public_pages_common_design_request_text($state);
		$this->save_public_pages_state($ctl, $state);
		$prompt = $this->build_public_pages_common_design_prompt_text($state);
		$ctl->set_session("wizard_current_prompt", $prompt);
		$this->open_codex_terminal_with_prompt($ctl);
	}

	function open_embed_app_wizard(Controller $ctl) {
		$state = [
			"embed_action" => "",
			"embed_app_id" => "",
			"embed_key" => "",
			"title" => "",
			"class_name" => "",
			"request_text" => "",
			"snippet_code" => ""
		];
		$this->save_embed_app_state($ctl, $state);
		$this->show_embed_app_step_select($ctl, $state);
	}

	function submit_embed_app_action_next(Controller $ctl) {
		$action = trim((string) $ctl->POST("embed_action"));
		if ($action === "") {
			$ctl->res_error_message("embed_action", $ctl->t("wizard.validation.choose_action"));
			return;
		}
		$state = $this->get_embed_app_state($ctl);
		$state["embed_action"] = $action;
		$state["embed_app_id"] = "";
		$state["embed_key"] = "";
		$state["title"] = "";
		$state["class_name"] = "";
		$state["request_text"] = "";
		$state["snippet_code"] = "";
		$this->save_embed_app_state($ctl, $state);
		if ($action === "add") {
			$this->show_embed_app_step_basic($ctl, $state);
			return;
		}
		if ($action === "edit" || $action === "delete" || $action === "show_code") {
			$this->show_embed_app_step_target($ctl, $state);
			return;
		}
		$ctl->show_notification_text($ctl->t("wizard.notification.preparing"), 3);
	}

	function submit_embed_app_target_next(Controller $ctl) {
		$embed_app_id = $this->normalize_single_id((string) $ctl->POST("embed_app_id"));
		if ($embed_app_id === "") {
			$action = (string) ($this->get_embed_app_state($ctl)["embed_action"] ?? "");
			if ($action === "delete") {
				$message = $ctl->t("wizard.validation.embed_app_delete_target_required");
			} elseif ($action === "show_code") {
				$message = $ctl->t("wizard.validation.embed_app_code_target_required");
			} else {
				$message = $ctl->t("wizard.validation.embed_app_edit_target_required");
			}
			$ctl->res_error_message("embed_app_id", $message);
			return;
		}
		$row = $this->get_embed_app_row($ctl, $embed_app_id);
		if ($row === null) {
			$ctl->res_error_message("embed_app_id", $ctl->t("wizard.validation.embed_app_target_not_found"));
			return;
		}
		$state = $this->get_embed_app_state($ctl);
		$state["embed_app_id"] = $embed_app_id;
		$state["embed_key"] = trim((string) ($row["embed_key"] ?? ""));
		$state["title"] = trim((string) ($row["title"] ?? ""));
		$state["class_name"] = trim((string) ($row["class_name"] ?? ""));
		$state["snippet_code"] = $this->build_embed_app_snippet_code($ctl, $state["embed_key"]);
		$this->save_embed_app_state($ctl, $state);
		if ((string) ($state["embed_action"] ?? "") === "delete") {
			$ctl->db("embed_app", "embed_app")->delete((int) $embed_app_id);
			$this->save_embed_app_state($ctl, []);
			$this->reflesh_all_screen($ctl);
			return;
		}
		if ((string) ($state["embed_action"] ?? "") === "show_code") {
			$this->show_embed_app_step_code($ctl, $state);
			return;
		}
		$this->show_embed_app_step_request($ctl, $state);
	}

	function submit_embed_app_basic_next(Controller $ctl) {
		$title = trim((string) $ctl->POST("title"));
		if ($title === "") {
			$ctl->res_error_message("title", $ctl->t("wizard.validation.embed_app_title_required"));
			return;
		}
		$state = $this->get_embed_app_state($ctl);
		$state["title"] = $title;
		$state["class_name"] = "";
		$state["embed_key"] = "";
		$this->save_embed_app_state($ctl, $state);
		$this->show_embed_app_step_request($ctl, $state);
	}

	function submit_embed_app_request_next(Controller $ctl) {
		$request_text = trim((string) $ctl->POST("request_text"));
		if ($request_text === "") {
			$ctl->res_error_message("request_text", $ctl->t("wizard.validation.request_text_required"));
			return;
		}
		$state = $this->get_embed_app_state($ctl);
		$state["request_text"] = $request_text;
		$this->save_embed_app_state($ctl, $state);
		$prompt = $this->build_embed_app_prompt_text($state);
		$ctl->set_session("wizard_current_prompt", $prompt);
		$this->open_codex_terminal_with_prompt($ctl);
	}

	function back_to_embed_app_basic(Controller $ctl) {
		$state = $this->get_embed_app_state($ctl);
		$this->show_embed_app_step_basic($ctl, $state);
	}

	function back_to_embed_app_select(Controller $ctl) {
		$state = $this->get_embed_app_state($ctl);
		$this->show_embed_app_step_select($ctl, $state);
	}

	function back_to_embed_app_target(Controller $ctl) {
		$state = $this->get_embed_app_state($ctl);
		$this->show_embed_app_step_target($ctl, $state);
	}

	function open_dashboard_wizard(Controller $ctl) {
		$state = [
			"dashboard_action" => "",
			"dashboard_id" => "",
			"title" => "",
			"class_name" => "",
			"function_name" => "dashboard",
			"column_width" => "1",
			"request_text" => ""
		];
		$this->save_dashboard_state($ctl, $state);
		$this->show_dashboard_step_select($ctl, $state);
	}

	function submit_dashboard_action_next(Controller $ctl) {
		$action = trim((string) $ctl->POST("dashboard_action"));
		if ($action === "") {
			$ctl->res_error_message("dashboard_action", $ctl->t("wizard.validation.choose_action"));
			return;
		}
		$state = $this->get_dashboard_state($ctl);
		$state["dashboard_action"] = $action;
		$state["dashboard_id"] = "";
		$state["title"] = "";
		$state["class_name"] = "";
		$state["function_name"] = "dashboard";
		$state["column_width"] = "1";
		$state["request_text"] = "";
		$this->save_dashboard_state($ctl, $state);
		if ($action === "add") {
			$this->show_dashboard_step_basic($ctl, $state);
			return;
		}
		if ($action === "edit" || $action === "delete") {
			$this->show_dashboard_step_target($ctl, $state);
			return;
		}
		$ctl->show_notification_text($ctl->t("wizard.notification.preparing"), 3);
	}

	function submit_dashboard_target_next(Controller $ctl) {
		$dashboard_id = $this->normalize_single_id((string) $ctl->POST("dashboard_id"));
		if ($dashboard_id === "") {
			$action = (string) ($this->get_dashboard_state($ctl)["dashboard_action"] ?? "");
			$ctl->res_error_message("dashboard_id", $action === "delete" ? $ctl->t("wizard.validation.dashboard_delete_target_required") : $ctl->t("wizard.validation.dashboard_edit_target_required"));
			return;
		}
		$row = $this->get_dashboard_row($ctl, $dashboard_id);
		if ($row === null) {
			$ctl->res_error_message("dashboard_id", $ctl->t("wizard.validation.dashboard_target_not_found"));
			return;
		}
		$state = $this->get_dashboard_state($ctl);
		$state["dashboard_id"] = $dashboard_id;
		$state["title"] = trim((string) ($row["class_name"] ?? ""));
		$state["class_name"] = trim((string) ($row["class_name"] ?? ""));
		$state["function_name"] = trim((string) ($row["function_name"] ?? ""));
		$state["column_width"] = (string) ((int) ($row["column_width"] ?? 1));
		$this->save_dashboard_state($ctl, $state);
		if ((string) ($state["dashboard_action"] ?? "") === "delete") {
			$ctl->db("dashboard", "dashboard")->delete((int) $dashboard_id);
			$this->save_dashboard_state($ctl, []);
			$this->reflesh_all_screen($ctl);
			return;
		}
		$this->show_dashboard_step_request($ctl, $state);
	}

	function submit_dashboard_basic_next(Controller $ctl) {
		$title = trim((string) $ctl->POST("title"));
		$column_width = (string) ((int) $ctl->POST("column_width"));
		if ($title === "") {
			$ctl->res_error_message("title", $ctl->t("wizard.validation.dashboard_title_required"));
			return;
		}
		if (!in_array($column_width, ["1", "2", "3"], true)) {
			$ctl->res_error_message("column_width", $ctl->t("wizard.validation.dashboard_width_required"));
			return;
		}
		$state = $this->get_dashboard_state($ctl);
		$state["title"] = $title;
		$state["class_name"] = "";
		$state["function_name"] = "dashboard";
		$state["column_width"] = $column_width;
		$this->save_dashboard_state($ctl, $state);
		$this->show_dashboard_step_request($ctl, $state);
	}

	function submit_dashboard_request_next(Controller $ctl) {
		$request_text = trim((string) $ctl->POST("request_text"));
		if ($request_text === "") {
			$ctl->res_error_message("request_text", $ctl->t("wizard.validation.request_text_required"));
			return;
		}
		$state = $this->get_dashboard_state($ctl);
		$state["request_text"] = $request_text;
		$this->save_dashboard_state($ctl, $state);
		$prompt = $this->build_dashboard_prompt_text($state);
		$ctl->set_session("wizard_current_prompt", $prompt);
		$this->open_codex_terminal_with_prompt($ctl);
	}

	function back_to_dashboard_select(Controller $ctl) {
		$state = $this->get_dashboard_state($ctl);
		$this->show_dashboard_step_select($ctl, $state);
	}

	function back_to_dashboard_target(Controller $ctl) {
		$state = $this->get_dashboard_state($ctl);
		$this->show_dashboard_step_target($ctl, $state);
	}

	function back_to_dashboard_basic(Controller $ctl) {
		$state = $this->get_dashboard_state($ctl);
		$this->show_dashboard_step_basic($ctl, $state);
	}

	function submit_public_pages_asset_add_exe(Controller $ctl) {
		$obj = getClassObject($ctl, "public_assets", new Dirs());
		$res = $obj->store_uploaded_asset($ctl, 1);
		if (!($res["ok"] ?? false)) {
			$ctl->clear_error_message();
			foreach (($res["errors"] ?? []) as $field => $message) {
				$ctl->res_error_message($field, $message);
			}
			return;
		}
		$state = $this->get_public_pages_state($ctl);
		$state["page_action"] = "";
		$this->save_public_pages_state($ctl, $state);
		$ctl->show_notification_text($ctl->t("wizard.notification.image_registered"), 2);
		$this->show_public_pages_step_select($ctl, $state);
	}

	function submit_public_pages_menu_manage_save(Controller $ctl) {
		$order_text = trim((string) $ctl->POST("menu_order"));
		$order = [];
		if ($order_text !== "") {
			foreach (explode(",", $order_text) as $id) {
				$id = $this->normalize_single_id($id);
				if ($id !== "") {
					$order[] = $id;
				}
			}
		}
		$menu_label_map = $ctl->POST("menu_label");
		$show_map = $ctl->POST("show_in_menu");
		if (!is_array($menu_label_map)) {
			$menu_label_map = [];
		}
		if (!is_array($show_map)) {
			$show_map = [];
		}
		if (count($order) === 0) {
			foreach (array_keys($menu_label_map) as $id) {
				$id = $this->normalize_single_id($id);
				if ($id !== "") {
					$order[] = $id;
				}
			}
		}
		$db = $ctl->db("public_pages_registry", "public_pages_registry");
		$sort = 0;
		foreach ($order as $id) {
			$row = $db->get((int) $id);
			if (!is_array($row) || count($row) === 0) {
				continue;
			}
			$row["show_in_menu"] = ((string) ($show_map[$id] ?? "0") === "1") ? 1 : 0;
			$row["menu_label"] = trim((string) ($menu_label_map[$id] ?? ""));
			$row["menu_sort"] = $sort;
			$row["updated_at"] = time();
			$db->update($row);
			$sort++;
		}
		$state = $this->get_public_pages_state($ctl);
		$state["page_action"] = "";
		$this->save_public_pages_state($ctl, $state);
		$ctl->show_notification_text($ctl->t("wizard.notification.menu_saved"), 2);
		$this->show_public_pages_step_select($ctl, $state);
	}

	function back_to_public_pages_select(Controller $ctl) {
		$state = $this->get_public_pages_state($ctl);
		$this->show_public_pages_step_select($ctl, $state);
	}

	function back_to_public_pages_add_info(Controller $ctl) {
		$state = $this->get_public_pages_state($ctl);
		$this->show_public_pages_step_add_info($ctl, $state);
	}

	function back_to_public_pages_target(Controller $ctl) {
		$state = $this->get_public_pages_state($ctl);
		$this->show_public_pages_step_target($ctl, $state);
	}

	function back_to_public_pages_assets(Controller $ctl) {
		$state = $this->get_public_pages_state($ctl);
		$this->show_public_pages_step_assets($ctl, $state);
	}

	function back_to_public_pages_common_header(Controller $ctl) {
		$state = $this->get_public_pages_state($ctl);
		$this->show_public_pages_step_common_header($ctl, $state);
	}

	function back_to_public_pages_common_footer(Controller $ctl) {
		$state = $this->get_public_pages_state($ctl);
		$this->show_public_pages_step_common_footer($ctl, $state);
	}

	function back_to_public_pages_common_nav(Controller $ctl) {
		$state = $this->get_public_pages_state($ctl);
		$this->show_public_pages_step_common_nav($ctl, $state);
	}

	function back_to_public_pages_common_style(Controller $ctl) {
		$state = $this->get_public_pages_state($ctl);
		$this->show_public_pages_step_common_style($ctl, $state);
	}

	function submit_line_bot_action_next(Controller $ctl) {
		$action = trim((string) $ctl->POST("line_action"));
		if ($action === "") {
			$ctl->res_error_message("line_action", $ctl->t("wizard.validation.choose_action"));
			return;
		}
		$state = $this->get_line_bot_state($ctl);
		$state["line_action"] = $action;
		$this->save_line_bot_state($ctl, $state);
		if ($action === "member_link") {
			$this->open_line_member_link_wizard($ctl);
			return;
		}
		if ($action === "connect") {
			$this->open_line_bot_connect_wizard($ctl);
			return;
		}
		if ($action === "edit") {
			$this->open_line_bot_edit_wizard($ctl);
			return;
		}
		if ($action === "delete") {
			$this->open_line_bot_delete_wizard($ctl);
			return;
		}
		if ($action !== "add") {
			$ctl->show_notification_text($ctl->t("wizard.notification.line_bot_preparing"), 3);
			return;
		}
		$this->show_line_bot_step_event($ctl, $state);
	}

	function submit_line_bot_event_next(Controller $ctl) {
		$event_type = trim((string) $ctl->POST("event_type"));
		$event_opt = $this->get_line_bot_event_options();
		if (!isset($event_opt[$event_type])) {
			$ctl->res_error_message("event_type", $ctl->t("wizard.validation.event_type_required"));
			return;
		}
		$state = $this->get_line_bot_state($ctl);
		$state["event_type"] = $event_type;
		if ($event_type !== "keyword") {
			$state["keyword"] = "";
		}
		if ($event_type === "follow" && trim((string) ($state["request_text"] ?? "")) === "") {
			$state["request_text"] = $ctl->t("wizard.line_bot.request.default_follow");
		}
		$this->save_line_bot_state($ctl, $state);
		if ($event_type === "keyword") {
			$this->show_line_bot_step_keyword($ctl, $state);
			return;
		}
		$duplicate_keyword = $event_type === "unmatch" ? "[unmatch]" : "[follow]";
		$duplicate = $this->find_line_bot_duplicate_keyword($ctl, $duplicate_keyword);
		if ($duplicate !== null) {
			$ctl->res_error_message("event_type", $event_type === "unmatch" ? $ctl->t("wizard.validation.line_bot_unmatch_exists") : $ctl->t("wizard.validation.line_bot_follow_exists"));
			return;
		}
		$this->show_line_bot_step_request($ctl, $state);
	}

	function submit_line_bot_keyword_next(Controller $ctl) {
		$keyword = trim((string) $ctl->POST("keyword"));
		if ($keyword === "") {
			$ctl->res_error_message("keyword", $ctl->t("wizard.validation.keyword_required"));
			return;
		}
		$duplicate = $this->find_line_bot_duplicate_keyword($ctl, $keyword);
		if ($duplicate !== null) {
			$ctl->res_error_message("keyword", $ctl->t("wizard.validation.keyword_duplicated"));
			return;
		}
		$state = $this->get_line_bot_state($ctl);
		$state["keyword"] = $keyword;
		$this->save_line_bot_state($ctl, $state);
		$this->show_line_bot_step_request($ctl, $state);
	}

	function submit_line_bot_request_next(Controller $ctl) {
		$request_text = trim((string) $ctl->POST("request_text"));
		if ($request_text === "") {
			$ctl->res_error_message("request_text", $ctl->t("wizard.validation.request_text_required"));
			return;
		}
		$state = $this->get_line_bot_state($ctl);
		$state["button_title"] = $this->build_line_bot_default_title((string) ($state["event_type"] ?? ""));
		$state["request_text"] = $request_text;
		$this->save_line_bot_state($ctl, $state);

		$prompt = $this->build_line_bot_add_prompt_text($state);
		$ctl->set_session("wizard_current_prompt", "");
		$ctl->set_session("wizard_table_create_prompt", "");
		$ctl->set_session("wizard_table_change_prompt", "");
		$ctl->set_session("codex_terminal_initial_input", $prompt);
		$ctl->close_multi_dialog("wizard");
		$ctl->invoke("run", [], "codex_terminal");
	}

	function back_to_line_bot_select(Controller $ctl) {
		$state = $this->get_line_bot_state($ctl);
		$this->show_line_bot_step_select($ctl, $state);
	}

	function open_line_bot_connect_wizard(Controller $ctl) {
		$setting = $this->get_line_bot_setting_row($ctl);
		$state = $this->get_line_bot_state($ctl);
		$state["line_action"] = "connect";
		$state["line_channel_secret"] = trim((string) ($setting["line_channel_secret"] ?? ""));
		$state["line_accesstoken"] = trim((string) ($setting["line_accesstoken"] ?? ""));
		$state["line_forward_unknown_to_manager"] = (string) ($setting["line_forward_unknown_to_manager"] ?? "0");
		$state["line_channel_secret_saved"] = $state["line_channel_secret"] === "" ? "0" : "1";
		$state["line_accesstoken_saved"] = $state["line_accesstoken"] === "" ? "0" : "1";
		$this->save_line_bot_state($ctl, $state);
		$this->show_line_bot_connect_step_intro($ctl, $state);
	}

	function submit_line_bot_connect_intro_next(Controller $ctl) {
		$state = $this->get_line_bot_state($ctl);
		$this->show_line_bot_connect_step_webhook($ctl, $state);
	}

	function submit_line_bot_connect_webhook_next(Controller $ctl) {
		$state = $this->get_line_bot_state($ctl);
		$this->show_line_bot_connect_step_response($ctl, $state);
	}

	function submit_line_bot_connect_response_next(Controller $ctl) {
		$state = $this->get_line_bot_state($ctl);
		$this->show_line_bot_connect_step_credentials($ctl, $state);
	}

	function submit_line_bot_connect_credentials_next(Controller $ctl) {
		$state = $this->get_line_bot_state($ctl);
		$line_channel_secret = trim((string) $ctl->POST("line_channel_secret"));
		$line_accesstoken = trim((string) $ctl->POST("line_accesstoken"));

		if ($line_channel_secret === "" && (string) ($state["line_channel_secret_saved"] ?? "0") !== "1") {
			$ctl->res_error_message("line_channel_secret", $ctl->t("wizard.validation.line_channel_secret_required"));
			return;
		}
		if ($line_accesstoken === "" && (string) ($state["line_accesstoken_saved"] ?? "0") !== "1") {
			$ctl->res_error_message("line_accesstoken", $ctl->t("wizard.validation.line_access_token_required"));
			return;
		}

		if ($line_channel_secret !== "") {
			$state["line_channel_secret"] = $line_channel_secret;
			$state["line_channel_secret_saved"] = "1";
		}
		if ($line_accesstoken !== "") {
			$state["line_accesstoken"] = $line_accesstoken;
			$state["line_accesstoken_saved"] = "1";
		}

		$this->save_line_bot_state($ctl, $state);
		$this->show_line_bot_connect_step_greeting($ctl, $state);
	}

	function submit_line_bot_connect_save(Controller $ctl) {
		$state = $this->get_line_bot_state($ctl);
		$line_forward_unknown_to_manager = (string) $ctl->POST("line_forward_unknown_to_manager");
		$options = $this->get_line_forward_unknown_to_manager_options($ctl);
		if (!isset($options[$line_forward_unknown_to_manager])) {
			$ctl->res_error_message("line_forward_unknown_to_manager", $ctl->t("wizard.validation.select_required"));
			return;
		}
		$state["line_forward_unknown_to_manager"] = $line_forward_unknown_to_manager;
		$this->save_line_bot_state($ctl, $state);

		$setting = $this->get_line_bot_setting_row($ctl);
		$setting["line_channel_secret"] = (string) ($state["line_channel_secret"] ?? "");
		$setting["line_accesstoken"] = (string) ($state["line_accesstoken"] ?? "");
		$setting["line_forward_unknown_to_manager"] = (int) ($state["line_forward_unknown_to_manager"] ?? "0");

		$db = $ctl->db("setting", "setting");
		if (!isset($setting["id"])) {
			$setting["id"] = 1;
		}
		if ((int) ($setting["id"] ?? 0) > 0) {
			$db->update($setting);
		} else {
			$db->insert($setting);
		}
		$ctl->set_session("setting", $setting);
		$ctl->close_multi_dialog("wizard");
		$ctl->show_notification_text($ctl->t("wizard.notification.line_connect_saved"), 2);
	}

	function back_to_line_bot_connect_intro(Controller $ctl) {
		$state = $this->get_line_bot_state($ctl);
		$this->show_line_bot_connect_step_intro($ctl, $state);
	}

	function back_to_line_bot_connect_webhook(Controller $ctl) {
		$state = $this->get_line_bot_state($ctl);
		$this->show_line_bot_connect_step_webhook($ctl, $state);
	}

	function back_to_line_bot_connect_response(Controller $ctl) {
		$state = $this->get_line_bot_state($ctl);
		$this->show_line_bot_connect_step_webhook($ctl, $state);
	}

	function back_to_line_bot_connect_credentials(Controller $ctl) {
		$state = $this->get_line_bot_state($ctl);
		$this->show_line_bot_connect_step_credentials($ctl, $state);
	}

	function back_to_line_bot_event(Controller $ctl) {
		$state = $this->get_line_bot_state($ctl);
		$this->show_line_bot_step_event($ctl, $state);
	}

	function back_to_line_bot_keyword(Controller $ctl) {
		$state = $this->get_line_bot_state($ctl);
		$this->show_line_bot_step_keyword($ctl, $state);
	}

	function open_line_member_link_wizard(Controller $ctl) {
		$state = [
			"db_id" => "",
			"tb_name" => "line_member",
			"user_id_field" => "userid",
			"display_name_field" => "line_name",
			"name_field" => "name",
			"create_if_missing" => "1",
			"button_title" => "LINE用会員DB作製",
			"request_text" =>
				"LINE user_id と会員DBを連結するための会員テーブルを作成してください。\n" .
				"対象テーブル: line_member\n" .
				"LINE user_id フィールド: userid\n" .
				"表示名フィールド: line_name\n" .
				"名前フィールド: name"
		];
		$this->save_line_member_link_state($ctl, $state);
		$this->show_line_member_link_step_table($ctl, $state);
	}

	function submit_line_member_link_table_next(Controller $ctl) {
		$state = $this->get_line_member_link_state($ctl);
		$tb_name = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		$list = $ctl->db("db", "db")->select("tb_name", $tb_name);
		if (is_array($list) && count($list) > 0) {
			$ctl->res_error_message("line_member_template", $ctl->t("wizard.validation.line_member_template_used", ["tb_name" => $tb_name]));
			return;
		}
		$this->save_line_member_link_state($ctl, $state);
		$prompt = $this->build_line_member_link_prompt_text($ctl, $state);
		$ctl->set_session("wizard_current_prompt", "");
		$ctl->set_session("wizard_table_create_prompt", "");
		$ctl->set_session("wizard_table_change_prompt", "");
		$ctl->set_session("codex_terminal_initial_input", $prompt);
		$ctl->close_multi_dialog("wizard");
		$ctl->invoke("run", [], "codex_terminal");
	}

	function back_to_line_member_link_table(Controller $ctl) {
		$state = $this->get_line_member_link_state($ctl);
		$this->show_line_member_link_step_table($ctl, $state);
	}

	function open_line_bot_delete_wizard(Controller $ctl) {
		$state = [
			"rule_id" => "",
			"event_type" => "",
			"keyword" => "",
			"action_class" => ""
		];
		$this->save_line_bot_delete_state($ctl, $state);
		$this->show_line_bot_delete_step_rule($ctl, $state);
	}

	function submit_line_bot_delete_next(Controller $ctl) {
		$rule_id = $this->normalize_single_id((string) $ctl->POST("rule_id"));
		if ($rule_id === "") {
			$ctl->res_error_message("rule_id", $ctl->t("wizard.validation.rule_delete_target_required"));
			return;
		}
		$rule = $this->get_line_bot_editable_rule($ctl, $rule_id);
		if ($rule === null) {
			$ctl->res_error_message("rule_id", $ctl->t("wizard.validation.rule_delete_target_not_found"));
			return;
		}
		$state = $this->get_line_bot_delete_state($ctl);
		$state["rule_id"] = $rule_id;
		$state["event_type"] = $this->detect_line_bot_rule_event_type($rule);
		$state["keyword"] = trim((string) ($rule["keyword"] ?? ""));
		$state["action_class"] = trim((string) ($rule["action_class"] ?? ""));
		$this->save_line_bot_delete_state($ctl, $state);

		$ctl->db("webhook_rule", "webhook_rule")->delete((int) $rule_id);
		$this->delete_line_bot_action_class_dir((string) ($state["action_class"] ?? ""));
		$this->save_line_bot_delete_state($ctl, []);
		$ctl->show_notification_text($ctl->t("wizard.notification.deleted"), 2);
		$this->reflesh_all_screen($ctl);
	}

	function open_line_bot_edit_wizard(Controller $ctl) {
		$state = [
			"rule_id" => "",
			"event_type" => "",
			"keyword" => "",
			"action_class" => "",
			"button_title" => "",
			"request_text" => ""
		];
		$this->save_line_bot_edit_state($ctl, $state);
		$this->show_line_bot_edit_step_rule($ctl, $state);
	}

	function submit_line_bot_edit_rule_next(Controller $ctl) {
		$rule_id = $this->normalize_single_id((string) $ctl->POST("rule_id"));
		if ($rule_id === "") {
			$ctl->res_error_message("rule_id", $ctl->t("wizard.validation.rule_edit_target_required"));
			return;
		}
		$rule = $this->get_line_bot_editable_rule($ctl, $rule_id);
		if ($rule === null) {
			$ctl->res_error_message("rule_id", $ctl->t("wizard.validation.rule_edit_target_not_found"));
			return;
		}
		$state = $this->get_line_bot_edit_state($ctl);
		$state["rule_id"] = $rule_id;
		$state["event_type"] = $this->detect_line_bot_rule_event_type($rule);
		$state["keyword"] = $state["event_type"] === "keyword" ? trim((string) ($rule["keyword"] ?? "")) : "";
		$state["action_class"] = trim((string) ($rule["action_class"] ?? ""));
		if ($state["button_title"] === "") {
			$state["button_title"] = $state["action_class"];
		}
		$this->save_line_bot_edit_state($ctl, $state);
		if ($state["event_type"] === "follow" || $state["event_type"] === "unmatch") {
			$this->show_line_bot_edit_step_request($ctl, $state);
			return;
		}
		if ($state["event_type"] === "keyword") {
			$this->show_line_bot_edit_step_keyword($ctl, $state);
			return;
		}
		$this->show_line_bot_edit_step_event($ctl, $state);
	}

	function submit_line_bot_edit_event_next(Controller $ctl) {
		$state = $this->get_line_bot_edit_state($ctl);
		$event_type = trim((string) $ctl->POST("event_type"));
		$event_opt = $this->get_line_bot_event_options();
		if (!isset($event_opt[$event_type])) {
			$ctl->res_error_message("event_type", $ctl->t("wizard.validation.event_type_required"));
			return;
		}
		$keyword = "";
		if ($event_type === "keyword") {
			$keyword = trim((string) $ctl->POST("keyword"));
			if ($keyword === "") {
				$ctl->res_error_message("keyword", $ctl->t("wizard.validation.keyword_required"));
				return;
			}
			$duplicate = $this->find_line_bot_duplicate_keyword_except($ctl, $keyword, (string) ($state["rule_id"] ?? ""));
			if ($duplicate !== null) {
				$ctl->res_error_message("keyword", $ctl->t("wizard.validation.keyword_duplicated"));
				return;
			}
		} else {
			$duplicate_keyword = $event_type === "unmatch" ? "[unmatch]" : "[follow]";
			$duplicate = $this->find_line_bot_duplicate_keyword_except($ctl, $duplicate_keyword, (string) ($state["rule_id"] ?? ""));
			if ($duplicate !== null) {
				$ctl->res_error_message("event_type", $event_type === "unmatch" ? $ctl->t("wizard.validation.line_bot_unmatch_exists") : $ctl->t("wizard.validation.line_bot_follow_exists"));
				return;
			}
		}
		$state["event_type"] = $event_type;
		$state["keyword"] = $keyword;
		$this->save_line_bot_edit_state($ctl, $state);
		$this->show_line_bot_edit_step_request($ctl, $state);
	}

	function submit_line_bot_edit_request_next(Controller $ctl) {
		$request_text = trim((string) $ctl->POST("request_text"));
		if ($request_text === "") {
			$ctl->res_error_message("request_text", $ctl->t("wizard.validation.request_text_required"));
			return;
		}
		$state = $this->get_line_bot_edit_state($ctl);
		$state["request_text"] = $request_text;
		$this->save_line_bot_edit_state($ctl, $state);

		$prompt = $this->build_line_bot_edit_prompt_text($state);
		$ctl->set_session("wizard_current_prompt", "");
		$ctl->set_session("wizard_table_create_prompt", "");
		$ctl->set_session("wizard_table_change_prompt", "");
		$ctl->set_session("codex_terminal_initial_input", $prompt);
		$ctl->close_multi_dialog("wizard");
		$ctl->invoke("run", [], "codex_terminal");
	}

	function back_to_line_bot_edit_rule(Controller $ctl) {
		$state = $this->get_line_bot_edit_state($ctl);
		$this->show_line_bot_edit_step_rule($ctl, $state);
	}

	function back_to_line_bot_edit_event(Controller $ctl) {
		$state = $this->get_line_bot_edit_state($ctl);
		if ((string) ($state["event_type"] ?? "") === "follow") {
			$this->show_line_bot_edit_step_rule($ctl, $state);
			return;
		}
		if ((string) ($state["event_type"] ?? "") === "keyword") {
			$this->show_line_bot_edit_step_rule($ctl, $state);
			return;
		}
		$this->show_line_bot_edit_step_event($ctl, $state);
	}

	function submit_original_form_table_next(Controller $ctl) {
		$db_id = (string) $ctl->POST("db_id");
		$db_id = $this->normalize_single_id($db_id);
		if ($db_id === "") {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.button_table_required"));
			return;
		}
		$db = $ctl->db("db", "db")->get($db_id);
		if (!is_array($db) || count($db) === 0) {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.target_table_not_found"));
			return;
		}
		$tb_name = $this->normalize_table_name((string) ($db["tb_name"] ?? ""));
		if ($tb_name === "") {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.invalid_target_tb_name"));
			return;
		}
		$state = $this->get_original_form_state($ctl);
		$state["db_id"] = $db_id;
		$state["tb_name"] = $tb_name;
		$state["parent_tb_id"] = (int) ($db["parent_tb_id"] ?? 0);
		$place_opt = $this->get_original_form_place_options($state["parent_tb_id"] > 0);
		if (!isset($place_opt[$state["place"]])) {
			$state["place"] = (string) array_key_first($place_opt);
		}
		$this->save_original_form_state($ctl, $state);
		$this->show_original_form_step_place($ctl, $state);
	}

	function submit_original_form_place_next(Controller $ctl) {
		$state = $this->get_original_form_state($ctl);
		$place = (string) $ctl->POST("place");
		$place_opt = $this->get_original_form_place_options(((int) ($state["parent_tb_id"] ?? 0)) > 0);
		if (!isset($place_opt[$place])) {
			$ctl->res_error_message("place", $ctl->t("wizard.validation.place_required"));
			return;
		}
		$state["place"] = $place;
		$this->save_original_form_state($ctl, $state);
		$this->show_original_form_step_request($ctl, $state);
	}

	function submit_original_form_request_next(Controller $ctl) {
		$button_title = trim((string) $ctl->POST("button_title"));
		if ($button_title === "") {
			$ctl->res_error_message("button_title", $ctl->t("wizard.validation.button_title_required"));
			return;
		}
		$request_text = trim((string) $ctl->POST("request_text"));
		if ($request_text === "") {
			$ctl->res_error_message("request_text", $ctl->t("wizard.validation.request_text_required"));
			return;
		}
		$state = $this->get_original_form_state($ctl);
		$state["button_title"] = $button_title;
		$state["request_text"] = $request_text;
		$this->save_original_form_state($ctl, $state);

		$prompt = $this->build_original_form_prompt_text($state);
		$ctl->set_session("wizard_current_prompt", "");
		$ctl->set_session("wizard_table_create_prompt", "");
		$ctl->set_session("wizard_table_change_prompt", "");
		$ctl->set_session("codex_terminal_initial_input", $prompt);
		$ctl->close_multi_dialog("wizard");
		$ctl->invoke("run", [], "codex_terminal");
	}

	function back_to_original_form_table(Controller $ctl) {
		$state = $this->get_original_form_state($ctl);
		$this->show_original_form_step_table($ctl, $state);
	}

	function back_to_original_form_place(Controller $ctl) {
		$state = $this->get_original_form_state($ctl);
		$this->show_original_form_step_place($ctl, $state);
	}

	function back_to_db_additionals_select(Controller $ctl) {
		$state = $this->get_db_additionals_state($ctl);
		$this->show_db_additionals_select($ctl, $state);
	}

	function open_db_additionals_edit_home_wizard(Controller $ctl) {
		$state = [
			"manage_action" => "",
			"additional_id" => "",
			"tb_name" => "",
			"note_name" => "",
			"button_title" => "",
			"class_name" => "",
			"function_name" => "",
			"place" => "",
			"request_text" => ""
		];
		$this->save_db_additionals_edit_state($ctl, $state);
		$this->show_db_additionals_manage_select($ctl, $state);
	}

	function submit_db_additionals_manage_action_next(Controller $ctl) {
		$action = trim((string) $ctl->POST("manage_action"));
		if (!in_array($action, ["edit", "delete", "sort"], true)) {
			$ctl->res_error_message("manage_action", $ctl->t("wizard.validation.additional_manage_action_required"));
			return;
		}
		$state = $this->get_db_additionals_edit_state($ctl);
		$state["manage_action"] = $action;
		$this->save_db_additionals_edit_state($ctl, $state);
		if ($action === "edit") {
			$this->show_db_additionals_edit_step_target($ctl, $state);
			return;
		}
		if ($action === "delete") {
			$this->open_db_additionals_delete_wizard($ctl);
			return;
		}
		$this->open_db_additionals_sort_wizard($ctl);
	}

	function back_to_db_additionals_manage_select(Controller $ctl) {
		$state = $this->get_db_additionals_edit_state($ctl);
		$this->show_db_additionals_manage_select($ctl, $state);
	}

	function submit_db_additionals_edit_target_next(Controller $ctl) {
		$additional_id = $this->normalize_single_id((string) $ctl->POST("additional_id"));
		if ($additional_id === "") {
			$ctl->res_error_message("additional_id", $ctl->t("wizard.validation.additional_button_target_required"));
			return;
		}
		$item = $this->get_db_additionals_target($ctl, $additional_id);
		if ($item === null) {
			$ctl->res_error_message("additional_id", $ctl->t("wizard.validation.additional_button_target_not_found"));
			return;
		}
		$state = $this->get_db_additionals_edit_state($ctl);
		$state = array_merge($state, $this->build_db_additionals_target_state($ctl, $item));
		$this->save_db_additionals_edit_state($ctl, $state);
		$this->show_db_additionals_edit_step_request($ctl, $state);
	}

	function submit_db_additionals_edit_request_next(Controller $ctl) {
		$request_text = trim((string) $ctl->POST("request_text"));
		if ($request_text === "") {
			$ctl->res_error_message("request_text", $ctl->t("wizard.validation.request_text_required"));
			return;
		}
		$state = $this->get_db_additionals_edit_state($ctl);
		$state["request_text"] = $request_text;
		$this->save_db_additionals_edit_state($ctl, $state);
		$prompt = $this->build_db_additionals_edit_prompt_text($ctl, $state);
		$ctl->set_session("wizard_current_prompt", "");
		$ctl->set_session("wizard_table_create_prompt", "");
		$ctl->set_session("wizard_table_change_prompt", "");
		$ctl->set_session("codex_terminal_initial_input", $prompt);
		$ctl->close_multi_dialog("wizard");
		$ctl->invoke("run", [], "codex_terminal");
	}

	function back_to_db_additionals_edit_target(Controller $ctl) {
		$state = $this->get_db_additionals_edit_state($ctl);
		$this->show_db_additionals_edit_step_target($ctl, $state);
	}

	function open_db_additionals_delete_wizard(Controller $ctl) {
		$state = [
			"additional_id" => "",
			"tb_name" => "",
			"note_name" => "",
			"button_title" => "",
			"class_name" => "",
			"function_name" => "",
			"place" => ""
		];
		$this->save_db_additionals_delete_state($ctl, $state);
		$this->show_db_additionals_delete_step_target($ctl, $state);
	}

	function submit_db_additionals_delete_target_next(Controller $ctl) {
		$additional_id = $this->normalize_single_id((string) $ctl->POST("additional_id"));
		if ($additional_id === "") {
			$ctl->res_error_message("additional_id", $ctl->t("wizard.validation.additional_button_delete_target_required"));
			return;
		}
		$item = $this->get_db_additionals_target($ctl, $additional_id);
		if ($item === null) {
			$ctl->res_error_message("additional_id", $ctl->t("wizard.validation.additional_button_target_not_found"));
			return;
		}
		$state = $this->build_db_additionals_target_state($ctl, $item);
		$this->save_db_additionals_delete_state($ctl, $state);
		$this->show_db_additionals_delete_step_confirm($ctl, $state);
	}

	function submit_db_additionals_delete_exe(Controller $ctl) {
		$state = $this->get_db_additionals_delete_state($ctl);
		$additional_id = $this->normalize_single_id($state["additional_id"] ?? "");
		if ($additional_id === "") {
			$ctl->show_notification_text($ctl->t("wizard.validation.additional_button_target_not_found"), 3);
			return;
		}
		$ctl->invoke("delete_exe", ["id" => $additional_id], "db_additionals");
		$ctl->reload_work_area();
		$ctl->reload_side_panel();
		$ctl->close_multi_dialog("wizard");
	}

	function back_to_db_additionals_delete_target(Controller $ctl) {
		$state = $this->get_db_additionals_delete_state($ctl);
		$this->show_db_additionals_delete_step_target($ctl, $state);
	}

	function open_db_additionals_sort_wizard(Controller $ctl) {
		$state = [
			"db_id" => "",
			"tb_name" => "",
			"parent_tb_id" => 0,
			"place" => ""
		];
		$this->save_db_additionals_sort_state($ctl, $state);
		$this->show_db_additionals_sort_step_table($ctl, $state);
	}

	function submit_db_additionals_sort_table_next(Controller $ctl) {
		$db_id = $this->normalize_single_id((string) $ctl->POST("db_id"));
		if ($db_id === "") {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.button_table_required"));
			return;
		}
		$db = $ctl->db("db", "db")->get($db_id);
		if (!is_array($db) || count($db) === 0) {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.target_table_not_found"));
			return;
		}
		$tb_name = $this->normalize_table_name((string) ($db["tb_name"] ?? ""));
		if ($tb_name === "") {
			$ctl->res_error_message("db_id", $ctl->t("wizard.validation.target_table_not_found"));
			return;
		}
		$state = $this->get_db_additionals_sort_state($ctl);
		$state["db_id"] = $db_id;
		$state["tb_name"] = $tb_name;
		$state["parent_tb_id"] = (int) ($db["parent_tb_id"] ?? 0);
		$place_opt = $this->get_original_form_place_options($state["parent_tb_id"] > 0);
		if (!isset($place_opt[$state["place"]])) {
			$state["place"] = (string) array_key_first($place_opt);
		}
		$this->save_db_additionals_sort_state($ctl, $state);
		$this->show_db_additionals_sort_step_place($ctl, $state);
	}

	function submit_db_additionals_sort_place_next(Controller $ctl) {
		$state = $this->get_db_additionals_sort_state($ctl);
		$place = (string) $ctl->POST("place");
		$place_opt = $this->get_original_form_place_options(((int) ($state["parent_tb_id"] ?? 0)) > 0);
		if (!isset($place_opt[$place])) {
			$ctl->res_error_message("place", $ctl->t("wizard.validation.place_required"));
			return;
		}
		$state["place"] = $place;
		$this->save_db_additionals_sort_state($ctl, $state);
		$this->show_db_additionals_sort_step_list($ctl, $state);
	}

	function submit_db_additionals_sort_finish(Controller $ctl) {
		$state = $this->get_db_additionals_sort_state($ctl);
		$place = (string) ($state["place"] ?? "");
		if ($place === "0" || $place === "1") {
			$ctl->reload_work_area();
		} else {
			$ctl->reload_side_panel();
		}
		$ctl->close_multi_dialog("wizard");
	}

	function back_to_db_additionals_sort_table(Controller $ctl) {
		$state = $this->get_db_additionals_sort_state($ctl);
		$this->show_db_additionals_sort_step_table($ctl, $state);
	}

	function back_to_db_additionals_sort_place(Controller $ctl) {
		$state = $this->get_db_additionals_sort_state($ctl);
		$this->show_db_additionals_sort_step_place($ctl, $state);
	}

	function open_table_change_wizard(Controller $ctl) {
		$state = [
			"change_action" => "",
			"target_tb_name" => "",
			"fields_text" => "",
			"display_matrix" => [],
			"delete_field_ids" => [],
			"update_field_ids" => [],
			"update_field_change_text" => "",
			"screen_add_field_ids" => []
		];
		$this->save_table_change_state($ctl, $state);
		$this->show_table_change_step_select($ctl, $state);
	}

	function submit_table_change_action_next(Controller $ctl) {
		$action = trim((string) $ctl->POST("change_action"));
		if ($action === "") {
			$ctl->res_error_message("change_action", $ctl->t("wizard.validation.change_action_required"));
			return;
		}
		$state = $this->get_table_change_state($ctl);
		$state["change_action"] = $action;
		$this->save_table_change_state($ctl, $state);
		if (!in_array($action, ["add_field", "delete_field", "update_field", "add_screen_field"], true)) {
			$ctl->show_notification_text($ctl->t("wizard.notification.table_change_preparing"), 3);
			return;
		}
		$this->show_table_change_step_table($ctl, $state);
	}

	function submit_table_change_table_next(Controller $ctl) {
		$target = $this->normalize_table_name((string) $ctl->POST("target_tb_name"));
		if ($target === "") {
			$ctl->res_error_message("target_tb_name", $ctl->t("wizard.validation.target_table_required"));
			return;
		}
		$list = $ctl->db("db", "db")->select("tb_name", $target);
		if (!is_array($list) || count($list) === 0) {
			$ctl->res_error_message("target_tb_name", $ctl->t("wizard.validation.target_table_not_found"));
			return;
		}
		$state = $this->get_table_change_state($ctl);
		$state["target_tb_name"] = $target;
		$this->save_table_change_state($ctl, $state);
		if (($state["change_action"] ?? "") === "delete_field") {
			$this->show_table_change_step_delete_fields($ctl, $state);
			return;
		}
		if (($state["change_action"] ?? "") === "update_field") {
			$this->show_table_change_step_update_field($ctl, $state);
			return;
		}
		if (($state["change_action"] ?? "") === "add_screen_field") {
			$options = $this->get_table_field_options($ctl, (string) ($state["target_tb_name"] ?? ""));
			$state["fields_text"] = implode("\n", array_values($options));
			$state["display_matrix"] = $this->build_screen_display_matrix_from_existing($ctl, (string) ($state["target_tb_name"] ?? ""), $state["fields_text"]);
			$this->save_table_change_state($ctl, $state);
			$this->show_table_change_step_display($ctl, $state);
			return;
		}
		$this->show_table_change_step_fields($ctl, $state);
	}

	function submit_table_change_fields_next(Controller $ctl) {
		$fields_text = trim((string) $ctl->POST("fields_text"));
		if ($fields_text === "") {
			$ctl->res_error_message("fields_text", $ctl->t("wizard.validation.fields_text_required"));
			return;
		}
		$duplicate_input = $this->find_duplicate_in_input_fields($fields_text);
		if ($duplicate_input !== "") {
			$ctl->res_error_message("fields_text", $ctl->t("wizard.validation.duplicate_fields", ["fields" => $duplicate_input]));
			return;
		}
		$state = $this->get_table_change_state($ctl);
		$existing_conflict = $this->find_existing_field_conflict($ctl, (string) ($state["target_tb_name"] ?? ""), $fields_text);
		if ($existing_conflict !== "") {
			$ctl->res_error_message("fields_text", $ctl->t("wizard.validation.existing_fields_duplicated", ["fields" => $existing_conflict]));
			return;
		}
		$state["fields_text"] = $this->normalize_fields_text_for_human($fields_text);
		$state["display_matrix"] = $this->build_default_display_matrix($state["fields_text"]);
		$this->save_table_change_state($ctl, $state);
		$this->show_table_change_step_display($ctl, $state);
	}

	function submit_table_change_display_next(Controller $ctl) {
		$state = $this->get_table_change_state($ctl);
		if ((string) ($state["change_action"] ?? "") === "add_screen_field") {
			$state["display_matrix"] = $this->build_screen_display_matrix_from_post($ctl, (string) ($state["fields_text"] ?? ""));
			$display_order = trim((string) $ctl->POST("display_order"));
			$reordered = $this->reorder_display_payload((string) ($state["fields_text"] ?? ""), $state["display_matrix"], $display_order);
			$state["fields_text"] = $reordered["fields_text"];
			$state["display_matrix"] = $reordered["display_matrix"];
			$this->save_table_change_state($ctl, $state);
			$this->replace_screen_fields_setting($ctl, $state);
			$this->show_table_change_screen_done($ctl, $state);
			return;
		} else {
			$state["display_matrix"] = $this->build_display_matrix_from_post($ctl, (string) ($state["fields_text"] ?? ""), true);
		}
		$this->save_table_change_state($ctl, $state);
		$action = (string) ($state["change_action"] ?? "");
		$plan_lines = [];
		$prompt_text = "";
		$preview_tpl = "table_change_preview.tpl";
		$preview_title = $ctl->t("wizard.table_change.preview_add");
		if ($action === "add_screen_field") {
			$plan_lines = $this->build_table_change_add_screen_plan_lines($state);
			$prompt_text = $this->build_table_change_add_screen_prompt_text($state, $plan_lines);
			$preview_tpl = "table_change_screen_add_preview.tpl";
			$preview_title = $ctl->t("wizard.table_change.preview_screen_add");
		} else {
			$plan_lines = $this->build_table_change_add_plan_lines($state);
			$prompt_text = $this->build_table_change_add_prompt_text($state, $plan_lines);
		}
		$ctl->set_session("wizard_table_change_prompt", $prompt_text);
		$ctl->set_session("wizard_current_prompt", $prompt_text);
		$ctl->assign("row", $state);
		$ctl->assign("plan_lines", $plan_lines);
		$ctl->show_multi_dialog("wizard", $preview_tpl, $preview_title, 980);
	}

	function submit_table_change_delete_fields_next(Controller $ctl) {
		$state = $this->get_table_change_state($ctl);
		$selected = $ctl->POST("delete_field_ids");
		if (!is_array($selected) || count($selected) === 0) {
			$ctl->res_error_message("delete_field_ids", $ctl->t("wizard.validation.delete_fields_required"));
			return;
		}
		$valid_map = $this->get_table_field_options($ctl, (string) ($state["target_tb_name"] ?? ""));
		$ids = [];
		$lines = [];
		foreach ($selected as $id) {
			$key = (string) $id;
			if (!isset($valid_map[$key])) {
				continue;
			}
			$ids[] = $key;
			$lines[] = $valid_map[$key];
		}
		if (count($ids) === 0) {
			$ctl->res_error_message("delete_field_ids", $ctl->t("wizard.validation.selected_fields_not_found"));
			return;
		}
		$state["delete_field_ids"] = $ids;
		$state["fields_text"] = implode("\n", $lines);
		$this->save_table_change_state($ctl, $state);

		$plan_lines = $this->build_table_change_delete_plan_lines($state);
		$prompt_text = $this->build_table_change_delete_prompt_text($state, $plan_lines);
		$ctl->set_session("wizard_table_change_prompt", $prompt_text);
		$ctl->set_session("wizard_current_prompt", $prompt_text);
		$ctl->assign("row", $state);
		$ctl->assign("plan_lines", $plan_lines);
		$ctl->show_multi_dialog("wizard", "table_change_delete_preview.tpl", $ctl->t("wizard.table_change.preview_delete"), 980);
	}

	function submit_table_change_update_field_next(Controller $ctl) {
		$state = $this->get_table_change_state($ctl);
		$selected = $ctl->POST("update_field_ids");
		if (!is_array($selected) || count($selected) === 0) {
			$ctl->res_error_message("update_field_ids", $ctl->t("wizard.validation.update_field_required"));
			return;
		}
		$change_text = trim((string) $ctl->POST("update_field_change_text"));
		if ($change_text === "") {
			$ctl->res_error_message("update_field_change_text", $ctl->t("wizard.validation.change_text_required"));
			return;
		}
		$field_options = $this->get_table_field_options($ctl, (string) ($state["target_tb_name"] ?? ""));
		$ids = [];
		$lines = [];
		foreach ($selected as $id) {
			$key = (string) $id;
			if (!isset($field_options[$key])) {
				continue;
			}
			$ids[] = $key;
			$lines[] = $field_options[$key];
		}
		if (count($ids) === 0) {
			$ctl->res_error_message("update_field_ids", $ctl->t("wizard.validation.selected_field_not_found"));
			return;
		}

		$state["update_field_ids"] = $ids;
		$state["update_field_change_text"] = $change_text;
		$state["fields_text"] = implode("\n", $lines);
		$this->save_table_change_state($ctl, $state);

		$plan_lines = $this->build_table_change_update_plan_lines($state);
		$prompt_text = $this->build_table_change_update_prompt_text($state, $plan_lines);
		$ctl->set_session("wizard_table_change_prompt", $prompt_text);
		$ctl->set_session("wizard_current_prompt", $prompt_text);
		$ctl->assign("row", $state);
		$ctl->assign("plan_lines", $plan_lines);
		$ctl->show_multi_dialog("wizard", "table_change_update_preview.tpl", $ctl->t("wizard.table_change.preview_update"), 980);
	}

	function submit_table_change_screen_add_fields_next(Controller $ctl) {
		$state = $this->get_table_change_state($ctl);
		$selected = $ctl->POST("screen_add_field_ids");
		if (!is_array($selected) || count($selected) === 0) {
			$ctl->res_error_message("screen_add_field_ids", $ctl->t("wizard.validation.screen_add_fields_required"));
			return;
		}
		$valid_map = $this->get_table_field_options($ctl, (string) ($state["target_tb_name"] ?? ""));
		$ids = [];
		$lines = [];
		foreach ($selected as $id) {
			$key = (string) $id;
			if (!isset($valid_map[$key])) {
				continue;
			}
			$ids[] = $key;
			$lines[] = $valid_map[$key];
		}
		if (count($ids) === 0) {
			$ctl->res_error_message("screen_add_field_ids", $ctl->t("wizard.validation.selected_fields_not_found"));
			return;
		}
		$state["screen_add_field_ids"] = $ids;
		$state["fields_text"] = implode("\n", $lines);
		$state["display_matrix"] = $this->build_default_display_matrix($state["fields_text"]);
		$this->save_table_change_state($ctl, $state);
		$this->show_table_change_step_display($ctl, $state);
	}

	function back_to_table_change_select(Controller $ctl) {
		$state = $this->get_table_change_state($ctl);
		$this->show_table_change_step_select($ctl, $state);
	}

	function back_to_table_change_table(Controller $ctl) {
		$state = $this->get_table_change_state($ctl);
		$this->show_table_change_step_table($ctl, $state);
	}

	function back_to_table_change_fields(Controller $ctl) {
		$state = $this->get_table_change_state($ctl);
		$this->show_table_change_step_fields($ctl, $state);
	}

	function back_to_table_change_delete_fields(Controller $ctl) {
		$state = $this->get_table_change_state($ctl);
		$this->show_table_change_step_delete_fields($ctl, $state);
	}

	function back_to_table_change_update_field(Controller $ctl) {
		$state = $this->get_table_change_state($ctl);
		$this->show_table_change_step_update_field($ctl, $state);
	}

	function back_to_table_change_screen_add_fields(Controller $ctl) {
		$state = $this->get_table_change_state($ctl);
		$this->show_table_change_step_screen_add_fields($ctl, $state);
	}

	function back_to_table_change_display(Controller $ctl) {
		$state = $this->get_table_change_state($ctl);
		$this->show_table_change_step_display($ctl, $state);
	}

	function open_codex_terminal_with_prompt(Controller $ctl) {
		$prompt = trim((string) $ctl->POST("prompt_text"));
		if ($prompt === "") {
			$prompt = trim((string) $ctl->get_session("wizard_current_prompt"));
		}
		$prompt = html_entity_decode($prompt, ENT_QUOTES | ENT_HTML5, "UTF-8");
		if ($prompt === "") {
			$ctl->show_notification_text($ctl->t("wizard.notification.prompt_missing"), 3);
			return;
		}
		// 使い回しを防止するため、投入前に一旦クリアする。
		$ctl->set_session("wizard_current_prompt", "");
		$ctl->set_session("wizard_table_create_prompt", "");
		$ctl->set_session("wizard_table_change_prompt", "");
		$ctl->set_session("codex_terminal_initial_input", $prompt);
		$ctl->close_multi_dialog("wizard");
		$ctl->invoke("run", [], "codex_terminal");
	}

	function reflesh_all_screen(Controller $ctl) {
		$ctl->close_multi_dialog("wizard");
		$ctl->reload_menu();
		$ctl->reload_work_area();
		$ctl->reload_side_panel();
	}

	private function show_home(Controller $ctl) {
		$wizard_groups = [
			[
				"title" => $ctl->t("wizard.home.group.guide.title"),
				"icon_path" => "",
				"items" => [
					["label" => $ctl->t("wizard.home.group.guide.item_video"), "status" => "ready"]
				],
				"button_label" => $ctl->t("wizard.home.use_this_wizard"),
				"button_function" => "open_guide_video_dialog",
				"enabled" => 1
			],
			[
				"title" => $ctl->t("wizard.home.group.note.title"),
				"icon_path" => "css/images/wizard-icon001.png",
				"items" => [
					["label" => $ctl->t("wizard.home.group.note.item_add"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.note.item_edit"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.note.item_delete"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.note.item_child_add"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.note.item_parent_child"), "status" => "ready"],
				],
				"button_label" => $ctl->t("wizard.home.use_this_wizard"),
				"button_function" => "open_note_wizard",
				"enabled" => 1
			],
			[
				"title" => $ctl->t("wizard.home.group.table_change.title"),
				"icon_path" => "css/images/wizard-icon002.png",
				"items" => [
					["label" => $ctl->t("wizard.home.group.table_change.item_add"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.table_change.item_edit"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.table_change.item_delete"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.table_change.item_screen"), "status" => "ready"]
				],
				"button_label" => $ctl->t("wizard.home.use_this_wizard"),
				"button_function" => "open_table_change_wizard",
				"enabled" => 1
			],
			[
				"title" => $ctl->t("wizard.home.group.db_additionals.title"),
				"icon_path" => "css/images/wizard-icon003.png",
				"items" => [
					["label" => $ctl->t("wizard.home.group.db_additionals.item_original_form"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.db_additionals.item_pdf"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.db_additionals.item_csv_download"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.db_additionals.item_csv_upload"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.db_additionals.item_chart"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.db_additionals.item_line_message"), "status" => "ready"]
				],
				"button_label" => $ctl->t("wizard.home.use_this_wizard"),
				"button_function" => "open_db_additionals_wizard",
				"enabled" => 1
			],
			[
				"title" => $ctl->t("wizard.home.group.db_additionals_edit.title"),
				"icon_path" => "css/images/wizard-icon003.png",
				"items" => [
					["label" => $ctl->t("wizard.home.group.db_additionals_edit.item_edit"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.db_additionals_delete.item_delete"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.db_additionals_sort.item_sort"), "status" => "ready"]
				],
				"button_label" => $ctl->t("wizard.home.use_this_wizard"),
				"button_function" => "open_db_additionals_edit_home_wizard",
				"enabled" => 1
			],
			[
				"title" => $ctl->t("wizard.home.group.line_bot.title"),
				"icon_path" => "css/images/wizard-icon005.png",
				"items" => [
					["label" => $ctl->t("wizard.home.group.line_bot.item_member_link"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.line_bot.item_connect"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.line_bot.item_add"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.line_bot.item_edit"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.line_bot.item_delete"), "status" => "ready"]
				],
				"button_label" => $ctl->t("wizard.home.use_this_wizard"),
				"button_function" => "open_line_bot_wizard",
				"enabled" => 1
			],
			[
				"title" => $ctl->t("wizard.home.group.cron.title"),
				"icon_path" => "css/images/wizard-icon006.png",
				"items" => [
					["label" => $ctl->t("wizard.home.group.cron.item_add"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.cron.item_edit"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.cron.item_delete"), "status" => "ready"],
					["label" => $ctl->t("wizard.home.group.cron.item_start"), "status" => "ready"]
				],
				"button_label" => $ctl->t("wizard.home.use_this_wizard"),
				"button_function" => "open_cron_wizard",
				"enabled" => 1
			],
				[
					"title" => $ctl->t("wizard.home.group.public_pages.title"),
					"icon_path" => "css/images/wizard-icon004.png",
					"items" => [
						["label" => $ctl->t("wizard.home.group.public_pages.item_asset_add"), "status" => "ready"],
						["label" => $ctl->t("wizard.home.group.public_pages.item_common_design"), "status" => "ready"]
					],
					"button_label" => $ctl->t("wizard.home.use_this_wizard"),
					"button_function" => "open_public_pages_wizard",
					"enabled" => 1
				],
				[
					"title" => $ctl->t("wizard.home.group.embed_app.title"),
					"icon_path" => "css/images/wizard-icon007.png",
					"items" => [
						["label" => $ctl->t("wizard.home.group.embed_app.item_add"), "status" => "ready"],
						["label" => $ctl->t("wizard.home.group.embed_app.item_edit"), "status" => "ready"],
						["label" => $ctl->t("wizard.home.group.embed_app.item_delete"), "status" => "ready"],
						["label" => $ctl->t("wizard.home.group.embed_app.item_show_code"), "status" => "ready"]
					],
					"button_label" => $ctl->t("wizard.home.use_this_wizard"),
					"button_function" => "open_embed_app_wizard",
					"enabled" => 1
				],
				[
					"title" => $ctl->t("wizard.home.group.dashboard.title"),
					"icon_path" => "css/images/wizard-icon008.png",
					"items" => [
						["label" => $ctl->t("wizard.home.group.dashboard.item_add"), "status" => "ready"],
						["label" => $ctl->t("wizard.home.group.dashboard.item_edit"), "status" => "ready"],
						["label" => $ctl->t("wizard.home.group.dashboard.item_delete"), "status" => "ready"]
					],
					"button_label" => $ctl->t("wizard.home.use_this_wizard"),
					"button_function" => "open_dashboard_wizard",
					"enabled" => 1
				]
			];
		$wizard_groups = array_values(array_filter($wizard_groups, function ($group) {
			return !isset($group["visible"]) || (int) $group["visible"] === 1;
		}));
		$ctl->assign("wizard_groups", $wizard_groups);
		$ctl->show_multi_dialog("wizard", "home.tpl", $ctl->t("wizard.title"), 980, "_fixed_bar.tpl");
	}

	function open_guide_video_dialog(Controller $ctl) {
		$ctl->assign("guide_video_url", "https://www.youtube.com/embed/jJnvZktaIX8?rel=0");
		$ctl->show_multi_dialog("wizard_guide_video", "guide_video.tpl", $ctl->t("wizard.home.group.guide.title"), 960);
	}

	private function show_step_purpose(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("back_function", ((string) ($state["create_mode"] ?? "") === "child") ? "back_to_table_create_parent" : "run");
		$title = ((string) ($state["create_mode"] ?? "") === "child")
			? $ctl->t("wizard.note_create_child.step_purpose")
			: $ctl->t("wizard.note_create.step_purpose");
		$ctl->show_multi_dialog("wizard", "table_create_step_purpose.tpl", $title, 760);
	}

	private function show_note_step_select(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "note_select.tpl", $ctl->t("wizard.note.step_select"), 760);
	}

	private function show_table_create_parent_step(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("table_options", $this->get_parent_table_options($ctl));
		$ctl->show_multi_dialog("wizard", "table_create_step_parent.tpl", $ctl->t("wizard.note_create_child.step_parent"), 760);
	}

	private function show_note_edit_step_table(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("table_options", $this->get_table_options($ctl));
		$ctl->show_multi_dialog("wizard", "note_edit_step_table.tpl", $ctl->t("wizard.note_edit.step_table"), 760);
	}

	private function show_note_edit_step_basic(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("note_show_menu_options", $this->get_note_show_menu_options());
		$ctl->assign("note_sort_order_options", $this->get_note_sort_order_options());
		$ctl->assign("note_sortkey_options", $this->get_note_sortkey_options($ctl, (string) ($state["target_tb_name"] ?? "")));
		$ctl->assign("note_list_type_options", $this->get_note_list_type_options());
		$ctl->assign("note_toggle_options", $this->get_note_toggle_options());
		$ctl->assign("note_side_list_type_options", $this->get_note_side_list_type_options());
		$ctl->assign("cascade_delete_flag_options", $this->get_cascade_delete_flag_options());
		$ctl->assign("note_parent_icon_options", $this->get_note_parent_icon_options());
		$ctl->show_multi_dialog("wizard", "note_edit_step_basic.tpl", $ctl->t("wizard.note_edit.step_basic"), 820);
	}

	private function show_note_edit_preview(Controller $ctl, array $state) {
		$plan_lines = $this->build_note_edit_plan_lines($state);
		$prompt_text = $this->build_note_edit_prompt_text($state, $plan_lines);
		$ctl->set_session("wizard_current_prompt", $prompt_text);
		$ctl->assign("row", $state);
		$ctl->assign("plan_lines", $plan_lines);
		$ctl->assign("prompt_text", $prompt_text);
		$ctl->show_multi_dialog("wizard", "note_edit_preview.tpl", $ctl->t("wizard.note_edit.step_preview"), 900);
	}

	private function show_note_delete_step_table(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("table_options", $this->get_table_options($ctl));
		$ctl->show_multi_dialog("wizard", "note_delete_step_table.tpl", $ctl->t("wizard.note_delete.step_table"), 760);
	}

	private function show_note_delete_preview(Controller $ctl, array $state) {
		$plan_lines = $this->build_note_delete_plan_lines($state);
		$prompt_text = $this->build_note_delete_prompt_text($state, $plan_lines);
		$ctl->set_session("wizard_current_prompt", $prompt_text);
		$ctl->assign("row", $state);
		$ctl->assign("plan_lines", $plan_lines);
		$ctl->assign("prompt_text", $prompt_text);
		$ctl->show_multi_dialog("wizard", "note_delete_preview.tpl", $ctl->t("wizard.note_delete.step_preview"), 900);
	}

	private function show_parent_child_note_step_child(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("child_table_options", $this->get_child_table_options($ctl));
		$ctl->show_multi_dialog("wizard", "parent_child_note_step_child.tpl", $ctl->t("wizard.parent_child_note.step_child"), 760);
	}

	private function show_parent_child_note_step_basic(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("dropdown_item_options", $this->get_parent_dropdown_item_options($ctl, (string) ($state["parent_tb_name"] ?? "")));
		$ctl->assign("dropdown_item_display_type_options", $this->get_dropdown_item_display_type_options());
		$ctl->assign("cascade_delete_flag_options", $this->get_cascade_delete_flag_options());
		$ctl->show_multi_dialog("wizard", "parent_child_note_step_basic.tpl", $ctl->t("wizard.parent_child_note.step_basic"), 860);
	}

	private function show_parent_child_note_preview(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "parent_child_note_preview.tpl", $ctl->t("wizard.parent_child_note.step_preview"), 900);
	}

	private function show_step_table(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$title = ((string) ($state["create_mode"] ?? "") === "child")
			? $ctl->t("wizard.note_create_child.step_note_name")
			: $ctl->t("wizard.note_create.step_note_name");
		$ctl->show_multi_dialog("wizard", "table_create_step_table.tpl", $title, 760);
	}

	private function show_step_fields(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("table_create_field_type_options", $this->get_table_create_field_type_options());
		$ctl->assign("table_create_option_field_types_json", json_encode(["dropdown", "checkbox", "radio"], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		$title = ((string) ($state["create_mode"] ?? "") === "child")
			? $ctl->t("wizard.note_create_child.step_fields")
			: $ctl->t("wizard.note_create.step_fields");
		$ctl->show_multi_dialog("wizard", "table_create_step_fields.tpl", $title, 820);
	}

	private function show_step_display(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$plan_lines = $this->build_table_create_plan_lines($state);
		$prompt_text = $this->build_table_create_prompt_text($state, $plan_lines);
		$ctl->set_session("wizard_table_create_prompt", $prompt_text);
		$ctl->set_session("wizard_current_prompt", $prompt_text);
		$ctl->assign("plan_lines", $plan_lines);
		$ctl->assign("prompt_text", $prompt_text);
		$title = ((string) ($state["create_mode"] ?? "") === "child")
			? $ctl->t("wizard.note_create_child.step_preview")
			: $ctl->t("wizard.note_create.step_preview");
		$ctl->show_multi_dialog("wizard", "table_create_preview.tpl", $title, 900);
	}

	private function show_table_change_step_select(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "table_change_select.tpl", $ctl->t("wizard.table_change.step_select"), 760);
	}

	private function show_table_change_step_table(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("table_options", $this->get_table_options($ctl));
		$ctl->show_multi_dialog("wizard", "table_change_step_table.tpl", $ctl->t("wizard.table_change.step_table"), 760);
	}

	private function show_table_change_step_fields(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("table_create_field_type_options", $this->get_table_create_field_type_options());
		$ctl->show_multi_dialog("wizard", "table_change_step_fields.tpl", $ctl->t("wizard.table_change.step_fields"), 760);
	}

	private function show_table_change_step_display(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("back_function", "back_to_table_change_fields");
		$ctl->assign("is_screen_replace_mode", 0);
		if ((string) ($state["change_action"] ?? "") === "add_screen_field") {
			$ctl->assign("display_target_labels", $this->get_screen_display_target_labels());
			$ctl->assign("field_display_rows", $this->build_screen_field_display_rows($state));
			$ctl->assign("back_function", "back_to_table_change_table");
			$ctl->assign("is_screen_replace_mode", 1);
		} else {
			$ctl->assign("display_target_labels", $this->get_display_target_labels());
			$ctl->assign("field_display_rows", $this->build_field_display_rows($state));
		}
		$title = $ctl->t("wizard.table_change.step_display");
		if (($state["change_action"] ?? "") === "add_screen_field") {
			$title = $ctl->t("wizard.table_change.step_screen_add");
		}
		$ctl->show_multi_dialog("wizard", "table_change_step_display.tpl", $title, 760);
	}

	private function show_table_change_step_delete_fields(Controller $ctl, array $state) {
		$options = $this->get_table_field_options($ctl, (string) ($state["target_tb_name"] ?? ""));
		$ctl->assign("row", $state);
		$ctl->assign("delete_field_rows", $this->build_delete_field_rows($options, $state["delete_field_ids"] ?? []));
		$ctl->show_multi_dialog("wizard", "table_change_step_delete_fields.tpl", $ctl->t("wizard.table_change.step_delete_fields"), 760);
	}

	private function show_table_change_step_update_field(Controller $ctl, array $state) {
		$options = $this->get_table_field_options($ctl, (string) ($state["target_tb_name"] ?? ""));
		$ctl->assign("row", $state);
		$selected_map = array_fill_keys($state["update_field_ids"] ?? [], 1);
		$rows = [];
		foreach ($options as $id => $label) {
			$rows[] = [
				"id" => (string) $id,
				"label" => (string) $label,
				"checked" => isset($selected_map[(string) $id]) ? 1 : 0
			];
		}
		$ctl->assign("update_field_rows", $rows);
		$ctl->show_multi_dialog("wizard", "table_change_step_update_field.tpl", $ctl->t("wizard.table_change.step_update_field"), 760);
	}

	private function show_table_change_step_screen_add_fields(Controller $ctl, array $state) {
		$options = $this->get_table_field_options($ctl, (string) ($state["target_tb_name"] ?? ""));
		$ctl->assign("row", $state);
		$ctl->assign("screen_add_field_rows", $this->build_delete_field_rows($options, $state["screen_add_field_ids"] ?? []));
		$ctl->show_multi_dialog("wizard", "table_change_step_screen_add_fields.tpl", $ctl->t("wizard.table_change.step_screen_add_fields"), 760);
	}

	private function show_original_form_step_table(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("table_id_options", $this->get_table_id_options($ctl));
		$ctl->show_multi_dialog("wizard", "original_form_step_table.tpl", $ctl->t("wizard.original_form.step_table"), 760);
	}

	private function show_db_additionals_select(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "db_additionals_select.tpl", $ctl->t("wizard.db_additionals.step_select"), 760);
	}

	private function show_db_additionals_manage_select(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "db_additionals_manage_select.tpl", $ctl->t("wizard.db_additionals.step_manage_select"), 760);
	}
	private function show_db_additionals_edit_step_target(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("db_additionals_target_options", $this->get_db_additionals_target_options($ctl));
		$ctl->show_multi_dialog("wizard", "db_additionals_edit_step_target.tpl", $ctl->t("wizard.db_additionals.step_edit_target"), 900);
	}

	private function show_db_additionals_edit_step_request(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("db_additionals_place_label", $this->get_db_additionals_place_label((string) ($state["place"] ?? "")));
		$ctl->show_multi_dialog("wizard", "db_additionals_edit_step_request.tpl", $ctl->t("wizard.db_additionals.step_edit_request"), 900);
	}

	private function show_db_additionals_delete_step_target(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("db_additionals_target_options", $this->get_db_additionals_target_options($ctl));
		$ctl->show_multi_dialog("wizard", "db_additionals_delete_step_target.tpl", $ctl->t("wizard.db_additionals.step_delete_target"), 900);
	}

	private function show_db_additionals_delete_step_confirm(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("db_additionals_place_label", $this->get_db_additionals_place_label((string) ($state["place"] ?? "")));
		$ctl->show_multi_dialog("wizard", "db_additionals_delete_step_confirm.tpl", $ctl->t("wizard.db_additionals.step_delete_confirm"), 900);
	}

	private function show_db_additionals_sort_step_table(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("table_id_options", $this->get_table_id_options($ctl));
		$ctl->show_multi_dialog("wizard", "db_additionals_sort_step_table.tpl", $ctl->t("wizard.db_additionals.step_sort_table"), 760);
	}

	private function show_db_additionals_sort_step_place(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("place_options", $this->get_original_form_place_options(((int) ($state["parent_tb_id"] ?? 0)) > 0));
		$ctl->assign("is_child", ((int) ($state["parent_tb_id"] ?? 0)) > 0 ? 1 : 0);
		$ctl->show_multi_dialog("wizard", "db_additionals_sort_step_place.tpl", $ctl->t("wizard.db_additionals.step_sort_place"), 760);
	}

	private function show_db_additionals_sort_step_list(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("additionals", $this->get_db_additionals_sort_list($ctl, (string) ($state["tb_name"] ?? ""), (string) ($state["place"] ?? "")));
		$ctl->assign("db_additionals_place_label", $this->get_db_additionals_place_label((string) ($state["place"] ?? "")));
		$ctl->show_multi_dialog("wizard", "db_additionals_sort_step_list.tpl", $ctl->t("wizard.db_additionals.step_sort_list"), 900);
	}

	private function show_original_form_step_place(Controller $ctl, array $state) {
		$is_child = ((int) ($state["parent_tb_id"] ?? 0)) > 0;
		$ctl->assign("row", $state);
		$ctl->assign("is_child", $is_child ? 1 : 0);
		$ctl->assign("place_options", $this->get_original_form_place_options($is_child));
		$ctl->show_multi_dialog("wizard", "original_form_step_place.tpl", $ctl->t("wizard.original_form.step_place"), 760);
	}

	private function show_original_form_step_request(Controller $ctl, array $state) {
		$is_child = ((int) ($state["parent_tb_id"] ?? 0)) > 0;
		$ctl->assign("row", $state);
		$ctl->assign("is_child", $is_child ? 1 : 0);
		$ctl->assign("place_options", $this->get_original_form_place_options($is_child));
		$ctl->show_multi_dialog("wizard", "original_form_step_request.tpl", $ctl->t("wizard.original_form.step_request"), 760);
	}

	private function show_pdf_step_table(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("table_id_options", $this->get_table_id_options($ctl));
		$ctl->show_multi_dialog("wizard", "pdf_step_table.tpl", $ctl->t("wizard.pdf.step_table"), 760);
	}

	private function show_pdf_step_place(Controller $ctl, array $state) {
		$is_child = ((int) ($state["parent_tb_id"] ?? 0)) > 0;
		$ctl->assign("row", $state);
		$ctl->assign("is_child", $is_child ? 1 : 0);
		$ctl->assign("place_options", $this->get_original_form_place_options($is_child));
		$ctl->show_multi_dialog("wizard", "pdf_step_place.tpl", $ctl->t("wizard.pdf.step_place"), 760);
	}

	private function show_pdf_step_fields(Controller $ctl, array $state) {
		$rows = $this->get_table_field_detail_rows($ctl, (string) ($state["tb_name"] ?? ""));
		$ctl->assign("row", $state);
		$ctl->assign("pdf_field_rows", $this->build_pdf_field_rows($rows, $state["field_ids"] ?? []));
		$ctl->show_multi_dialog("wizard", "pdf_step_fields.tpl", $ctl->t("wizard.pdf.step_fields"), 900);
	}

	private function show_pdf_step_request(Controller $ctl, array $state) {
		$is_child = ((int) ($state["parent_tb_id"] ?? 0)) > 0;
		$rows = $this->get_table_field_detail_rows($ctl, (string) ($state["tb_name"] ?? ""));
		$ctl->assign("row", $state);
		$ctl->assign("is_child", $is_child ? 1 : 0);
		$ctl->assign("place_options", $this->get_original_form_place_options($is_child));
		$ctl->assign("pdf_selected_fields", $this->build_pdf_selected_field_rows($rows, $state["field_ids"] ?? []));
		$ctl->show_multi_dialog("wizard", "pdf_step_request.tpl", $ctl->t("wizard.pdf.step_request"), 900);
	}

	private function show_csv_download_step_table(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("table_id_options", $this->get_table_id_options($ctl));
		$ctl->show_multi_dialog("wizard", "csv_download_step_table.tpl", $ctl->t("wizard.csv_download.step_table"), 760);
	}

	private function show_csv_download_step_place(Controller $ctl, array $state) {
		$is_child = ((int) ($state["parent_tb_id"] ?? 0)) > 0;
		$ctl->assign("row", $state);
		$ctl->assign("is_child", $is_child ? 1 : 0);
		$ctl->assign("place_options", $this->get_original_form_place_options($is_child));
		$ctl->show_multi_dialog("wizard", "csv_download_step_place.tpl", $ctl->t("wizard.csv_download.step_place"), 760);
	}

	private function show_csv_download_step_fields(Controller $ctl, array $state) {
		$rows = $this->get_table_field_detail_rows($ctl, (string) ($state["tb_name"] ?? ""));
		$ctl->assign("row", $state);
		$ctl->assign("csv_field_rows", $this->build_pdf_field_rows($rows, $state["field_ids"] ?? []));
		$ctl->show_multi_dialog("wizard", "csv_download_step_fields.tpl", $ctl->t("wizard.csv_download.step_fields"), 900);
	}

	private function show_csv_download_step_request(Controller $ctl, array $state) {
		$is_child = ((int) ($state["parent_tb_id"] ?? 0)) > 0;
		$rows = $this->get_table_field_detail_rows($ctl, (string) ($state["tb_name"] ?? ""));
		$ctl->assign("row", $state);
		$ctl->assign("is_child", $is_child ? 1 : 0);
		$ctl->assign("place_options", $this->get_original_form_place_options($is_child));
		$ctl->assign("csv_selected_fields", $this->build_pdf_selected_field_rows($rows, $state["field_ids"] ?? []));
		$ctl->show_multi_dialog("wizard", "csv_download_step_request.tpl", $ctl->t("wizard.csv_download.step_request"), 900);
	}

	private function show_csv_upload_step_table(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("table_id_options", $this->get_table_id_options($ctl));
		$ctl->show_multi_dialog("wizard", "csv_upload_step_table.tpl", $ctl->t("wizard.csv_upload.step_table"), 760);
	}

	private function show_csv_upload_step_place(Controller $ctl, array $state) {
		$is_child = ((int) ($state["parent_tb_id"] ?? 0)) > 0;
		$ctl->assign("row", $state);
		$ctl->assign("is_child", $is_child ? 1 : 0);
		$ctl->assign("place_options", $this->get_original_form_place_options($is_child));
		$ctl->show_multi_dialog("wizard", "csv_upload_step_place.tpl", $ctl->t("wizard.csv_upload.step_place"), 760);
	}

	private function show_csv_upload_step_fields(Controller $ctl, array $state) {
		$rows = $this->get_table_field_detail_rows($ctl, (string) ($state["tb_name"] ?? ""));
		$ctl->assign("row", $state);
		$ctl->assign("csv_upload_field_rows", $this->build_pdf_field_rows($rows, $state["field_ids"] ?? []));
		$ctl->show_multi_dialog("wizard", "csv_upload_step_fields.tpl", $ctl->t("wizard.csv_upload.step_fields"), 900);
	}

	private function show_csv_upload_step_request(Controller $ctl, array $state) {
		$is_child = ((int) ($state["parent_tb_id"] ?? 0)) > 0;
		$rows = $this->get_table_field_detail_rows($ctl, (string) ($state["tb_name"] ?? ""));
		$ctl->assign("row", $state);
		$ctl->assign("is_child", $is_child ? 1 : 0);
		$ctl->assign("place_options", $this->get_original_form_place_options($is_child));
		$ctl->assign("csv_upload_selected_fields", $this->build_pdf_selected_field_rows($rows, $state["field_ids"] ?? []));
		$ctl->show_multi_dialog("wizard", "csv_upload_step_request.tpl", $ctl->t("wizard.csv_upload.step_request"), 900);
	}

	private function show_chart_step_table(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("table_id_options", $this->get_table_id_options($ctl));
		$ctl->show_multi_dialog("wizard", "chart_step_table.tpl", $ctl->t("wizard.chart.step_table"), 760);
	}

	private function show_chart_step_place(Controller $ctl, array $state) {
		$is_child = ((int) ($state["parent_tb_id"] ?? 0)) > 0;
		$ctl->assign("row", $state);
		$ctl->assign("is_child", $is_child ? 1 : 0);
		$ctl->assign("place_options", $this->get_original_form_place_options($is_child));
		$ctl->show_multi_dialog("wizard", "chart_step_place.tpl", $ctl->t("wizard.chart.step_place"), 760);
	}

	private function show_chart_step_type(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("chart_type_options", $this->get_chart_type_options());
		$ctl->show_multi_dialog("wizard", "chart_step_type.tpl", $ctl->t("wizard.chart.step_type"), 760);
	}

	private function show_chart_step_fields(Controller $ctl, array $state) {
		$rows = $this->get_table_field_detail_rows($ctl, (string) ($state["tb_name"] ?? ""));
		$ctl->assign("row", $state);
		$ctl->assign("chart_field_rows", $this->build_pdf_field_rows($rows, $state["field_ids"] ?? []));
		$ctl->show_multi_dialog("wizard", "chart_step_fields.tpl", $ctl->t("wizard.chart.step_fields"), 900);
	}

	private function show_chart_step_request(Controller $ctl, array $state) {
		$is_child = ((int) ($state["parent_tb_id"] ?? 0)) > 0;
		$rows = $this->get_table_field_detail_rows($ctl, (string) ($state["tb_name"] ?? ""));
		$ctl->assign("row", $state);
		$ctl->assign("is_child", $is_child ? 1 : 0);
		$ctl->assign("place_options", $this->get_original_form_place_options($is_child));
		$ctl->assign("chart_type_options", $this->get_chart_type_options());
		$ctl->assign("chart_selected_fields", $this->build_pdf_selected_field_rows($rows, $state["field_ids"] ?? []));
		$ctl->show_multi_dialog("wizard", "chart_step_request.tpl", $ctl->t("wizard.chart.step_request"), 900);
	}

	private function show_line_message_step_table(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("table_id_options", $this->get_table_id_options($ctl));
		$ctl->show_multi_dialog("wizard", "line_message_step_table.tpl", $ctl->t("wizard.line_message.step_table"), 760);
	}

	private function show_line_message_step_place(Controller $ctl, array $state) {
		$is_child = ((int) ($state["parent_tb_id"] ?? 0)) > 0;
		$ctl->assign("row", $state);
		$ctl->assign("is_child", $is_child ? 1 : 0);
		$ctl->assign("place_options", $this->get_original_form_place_options($is_child));
		$ctl->show_multi_dialog("wizard", "line_message_step_place.tpl", $ctl->t("wizard.line_message.step_place"), 760);
	}

	private function show_line_message_step_request(Controller $ctl, array $state) {
		$is_child = ((int) ($state["parent_tb_id"] ?? 0)) > 0;
		$ctl->assign("row", $state);
		$ctl->assign("is_child", $is_child ? 1 : 0);
		$ctl->assign("place_options", $this->get_original_form_place_options($is_child));
		$ctl->show_multi_dialog("wizard", "line_message_step_request.tpl", $ctl->t("wizard.line_message.step_request"), 900);
	}

	private function show_cron_step_select(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "cron_select.tpl", $ctl->t("wizard.cron.step_select"), 760);
	}

	private function show_cron_step_target(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("cron_id_options", $this->get_cron_options($ctl));
		$is_delete = ((string) ($state["cron_action"] ?? "")) === "delete";
		$ctl->assign("cron_target_mode", $is_delete ? "delete" : "edit");
		$title = $is_delete ? $ctl->t("wizard.cron.step_target_delete") : $ctl->t("wizard.cron.step_target_edit");
		$ctl->show_multi_dialog("wizard", "cron_step_target.tpl", $title, 900);
	}

	private function show_cron_step_timing(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$title = ((string) ($state["cron_action"] ?? "")) === "edit"
			? $ctl->t("wizard.cron.step_timing_edit")
			: $ctl->t("wizard.cron.step_timing_add");
		$ctl->show_multi_dialog("wizard", "cron_step_timing.tpl", $title, 900);
	}

	private function show_cron_step_start(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("cron_rows", $this->get_cron_list_rows($ctl));
		$ctl->show_multi_dialog("wizard", "cron_step_start.tpl", $ctl->t("wizard.cron.step_start"), 960);
	}

	private function show_public_pages_step_select(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "public_pages_select.tpl", $ctl->t("wizard.public_pages.step_select"), 760);
	}

	private function show_public_pages_step_add_info(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "public_pages_step_add_info.tpl", $ctl->t("wizard.public_pages.step_add_info"), 760);
	}

	private function show_public_pages_step_asset_add(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "public_pages_step_asset_add.tpl", $ctl->t("wizard.public_pages.step_asset_add"), 760);
	}

	private function show_public_pages_step_menu_manage(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("menu_manage_rows", $this->get_public_pages_menu_manage_rows($ctl));
		$ctl->assign("menu_show_options", [0 => "Hide", 1 => "Show"]);
		$ctl->show_multi_dialog("wizard", "public_pages_menu_manage.tpl", $ctl->t("wizard.public_pages.step_menu_manage"), 980);
	}

	private function show_public_pages_step_target(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("public_pages_registry_options", $this->get_public_pages_registry_options($ctl));
		$action = (string) ($state["page_action"] ?? "");
		$title = $action === "delete"
			? $ctl->t("wizard.public_pages.step_target_delete")
			: $ctl->t("wizard.public_pages.step_target_edit");
		$ctl->show_multi_dialog("wizard", "public_pages_step_target.tpl", $title, 900);
	}

	private function show_public_pages_step_assets(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("public_asset_rows", $this->get_public_asset_rows_for_wizard($ctl, $state["public_asset_ids"] ?? []));
		$ctl->assign("icon_path", "css/images/wizard-icon014.png");
		$action = (string) ($state["page_action"] ?? "");
		if ($action === "common_design") {
			$title = $ctl->t("wizard.public_pages.step_assets_common_design");
		} elseif ($action === "edit") {
			$title = $ctl->t("wizard.public_pages.step_assets_edit");
		} else {
			$title = $ctl->t("wizard.public_pages.step_assets_add");
		}
		$ctl->show_multi_dialog("wizard", "public_pages_step_assets.tpl", $title, 900);
	}

	private function show_public_pages_step_common_header(Controller $ctl, array $state) {
		$this->show_public_pages_step_common_text(
			$ctl,
			$state,
			$ctl->t("wizard.public_pages.step_common_header"),
			$ctl->t("wizard.public_pages.common_header"),
			"header_value",
			(string) ($state["common_header_text"] ?? ""),
			$ctl->t("wizard.public_pages.common_header_example"),
			"back_to_public_pages_assets",
			"submit_public_pages_common_header_next",
			"css/images/wizard-icon010.png"
		);
	}

	private function show_public_pages_step_common_footer(Controller $ctl, array $state) {
		$this->show_public_pages_step_common_text(
			$ctl,
			$state,
			$ctl->t("wizard.public_pages.step_common_footer"),
			$ctl->t("wizard.public_pages.common_footer"),
			"footer_value",
			(string) ($state["common_footer_text"] ?? ""),
			$ctl->t("wizard.public_pages.common_footer_example"),
			"back_to_public_pages_common_header",
			"submit_public_pages_common_footer_next",
			"css/images/wizard-icon011.png"
		);
	}

	private function show_public_pages_step_common_nav(Controller $ctl, array $state) {
		$this->show_public_pages_step_common_text(
			$ctl,
			$state,
			$ctl->t("wizard.public_pages.step_common_nav"),
			$ctl->t("wizard.public_pages.common_nav"),
			"nav_value",
			(string) ($state["common_nav_text"] ?? ""),
			$ctl->t("wizard.public_pages.common_nav_example"),
			"back_to_public_pages_common_footer",
			"submit_public_pages_common_nav_next",
			"css/images/wizard-icon012.png"
		);
	}

	private function show_public_pages_step_common_style(Controller $ctl, array $state) {
		$this->show_public_pages_step_common_text(
			$ctl,
			$state,
			$ctl->t("wizard.public_pages.step_common_style"),
			$ctl->t("wizard.public_pages.common_style"),
			"style_value",
			(string) ($state["common_style_text"] ?? ""),
			$ctl->t("wizard.public_pages.common_style_example"),
			"back_to_public_pages_common_nav",
			"submit_public_pages_common_style_next",
			"css/images/wizard-icon013.png"
		);
	}

	private function show_public_pages_step_common_text(
		Controller $ctl,
		array $state,
		string $title,
		string $step_label,
		string $field_name,
		string $value,
		string $example,
		string $back_function,
		string $next_function,
		string $icon_path = ""
	) {
		$ctl->assign("row", $state);
		$ctl->assign("selected_public_asset_rows", $this->build_selected_public_asset_rows($ctl, $state["public_asset_ids"] ?? []));
		$ctl->assign("step_label", $step_label);
		$ctl->assign("field_name", $field_name);
		$ctl->assign("field_value", $value);
		$ctl->assign("example_text", $example);
		$ctl->assign("back_function_name", $back_function);
		$ctl->assign("next_function_name", $next_function);
		$ctl->assign("icon_path", $icon_path);
		$ctl->assign("step_prompt", $ctl->t("wizard.public_pages.common_text.prompt", ["label" => $step_label]));
		$ctl->assign("example_prompt", $ctl->t("wizard.public_pages.common_text.example", ["example" => $example]));
		$ctl->show_multi_dialog("wizard", "public_pages_common_design_text.tpl", $title, 900);
	}

	private function show_public_pages_step_common_confirm(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("selected_public_asset_rows", $this->build_selected_public_asset_rows($ctl, $state["public_asset_ids"] ?? []));
		$ctl->show_multi_dialog("wizard", "public_pages_common_design_confirm.tpl", $ctl->t("wizard.public_pages.step_common_confirm"), 900);
	}

	private function show_public_pages_step_request(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("selected_public_asset_rows", $this->build_selected_public_asset_rows($ctl, $state["public_asset_ids"] ?? []));
		$action = (string) ($state["page_action"] ?? "");
		if ($action === "common_design") {
			$title = $ctl->t("wizard.public_pages.step_request_common_design");
		} elseif ($action === "edit") {
			$title = $ctl->t("wizard.public_pages.step_request_edit");
		} else {
			$title = $ctl->t("wizard.public_pages.step_request_add");
		}
		$ctl->show_multi_dialog("wizard", "public_pages_step_request.tpl", $title, 900);
	}

	private function show_embed_app_step_basic(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "embed_app_step_basic.tpl", $ctl->t("wizard.embed_app.step_basic"), 900);
	}

	private function show_embed_app_step_request(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$title = ((string) ($state["embed_action"] ?? "")) === "edit"
			? $ctl->t("wizard.embed_app.step_request_edit")
			: $ctl->t("wizard.embed_app.step_request_add");
		$ctl->show_multi_dialog("wizard", "embed_app_step_request.tpl", $title, 900);
	}

	private function show_embed_app_step_code(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("snippet_code", (string) ($state["snippet_code"] ?? ""));
		$ctl->show_multi_dialog("wizard", "embed_app_step_code.tpl", $ctl->t("wizard.embed_app.step_code"), 980);
	}

	private function show_embed_app_step_select(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "embed_app_select.tpl", $ctl->t("wizard.embed_app.step_select"), 760);
	}

	private function show_embed_app_step_target(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("embed_app_options", $this->get_embed_app_options($ctl));
		$action = (string) ($state["embed_action"] ?? "");
		if ($action === "delete") {
			$title = $ctl->t("wizard.embed_app.step_target_delete");
		} elseif ($action === "show_code") {
			$title = $ctl->t("wizard.embed_app.step_target_code");
		} else {
			$title = $ctl->t("wizard.embed_app.step_target");
		}
		$ctl->show_multi_dialog("wizard", "embed_app_step_target.tpl", $title, 900);
	}

	private function show_dashboard_step_select(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "dashboard_select.tpl", $ctl->t("wizard.dashboard.step_select"), 760);
	}

	private function show_dashboard_step_basic(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("dashboard_column_width_options", $this->get_dashboard_column_width_options());
		$ctl->show_multi_dialog("wizard", "dashboard_step_basic.tpl", $ctl->t("wizard.dashboard.step_basic"), 900);
	}

	private function show_dashboard_step_target(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("dashboard_options", $this->get_dashboard_options($ctl));
		$title = ((string) ($state["dashboard_action"] ?? "")) === "delete"
			? $ctl->t("wizard.dashboard.step_target_delete")
			: $ctl->t("wizard.dashboard.step_target");
		$ctl->show_multi_dialog("wizard", "dashboard_step_target.tpl", $title, 900);
	}

	private function show_dashboard_step_request(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("dashboard_column_width_label", $this->get_dashboard_column_width_options()[(int) ($state["column_width"] ?? 1)] ?? "1 column");
		$title = ((string) ($state["dashboard_action"] ?? "")) === "edit"
			? $ctl->t("wizard.dashboard.step_request_edit")
			: $ctl->t("wizard.dashboard.step_request_add");
		$ctl->show_multi_dialog("wizard", "dashboard_step_request.tpl", $title, 900);
	}

	private function show_line_bot_step_select(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "line_bot_select.tpl", $ctl->t("wizard.line_bot.step_select"), 760);
	}

	private function show_line_bot_connect_step_intro(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("line_manager_url", "https://manager.line.biz/");
		$ctl->show_multi_dialog("wizard", "line_bot_connect_step_intro.tpl", $ctl->t("wizard.line_bot.step_connect_intro"), 760);
	}

	private function show_line_bot_connect_step_webhook(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("line_webhook_url", $ctl->get_APP_URL("webhook_line", "receive"));
		$ctl->show_multi_dialog("wizard", "line_bot_connect_step_webhook.tpl", $ctl->t("wizard.line_bot.step_connect_webhook"), 760);
	}

	private function show_line_bot_connect_step_response(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "line_bot_connect_step_response.tpl", $ctl->t("wizard.line_bot.step_connect_response"), 760);
	}

	private function show_line_bot_connect_step_credentials(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "line_bot_connect_step_credentials.tpl", $ctl->t("wizard.line_bot.step_connect_credentials"), 760);
	}

	private function show_line_bot_connect_step_greeting(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("line_forward_unknown_to_manager_options", $this->get_line_forward_unknown_to_manager_options($ctl));
		$ctl->show_multi_dialog("wizard", "line_bot_connect_step_greeting.tpl", $ctl->t("wizard.line_bot.step_connect_greeting"), 760);
	}

	private function show_line_bot_step_event(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("line_bot_event_options", $this->get_line_bot_event_options());
		$ctl->show_multi_dialog("wizard", "line_bot_step_event.tpl", $ctl->t("wizard.line_bot.step_event"), 760);
	}

	private function show_line_bot_step_keyword(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "line_bot_step_keyword.tpl", $ctl->t("wizard.line_bot.step_keyword"), 760);
	}

	private function show_line_bot_step_request(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("line_bot_event_options", $this->get_line_bot_event_options());
		$ctl->assign("line_bot_keyword_preview", $this->build_line_bot_keyword_preview($state));
		$title = ((string) ($state["event_type"] ?? "")) === "keyword"
			? $ctl->t("wizard.line_bot.step_request_keyword")
			: $ctl->t("wizard.line_bot.step_request");
		$ctl->show_multi_dialog("wizard", "line_bot_step_request.tpl", $title, 900);
	}

	private function show_line_member_link_step_table(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "line_member_link_step_table.tpl", $ctl->t("wizard.line_bot.step_member_link"), 900);
	}

	private function show_line_bot_delete_step_rule(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("line_bot_delete_rule_options", $this->get_line_bot_edit_rule_options($ctl));
		$ctl->show_multi_dialog("wizard", "line_bot_delete_step_rule.tpl", $ctl->t("wizard.line_bot.step_delete_rule"), 900);
	}

	private function show_line_bot_edit_step_rule(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("line_bot_edit_rule_options", $this->get_line_bot_edit_rule_options($ctl));
		$ctl->show_multi_dialog("wizard", "line_bot_edit_step_rule.tpl", $ctl->t("wizard.line_bot.step_edit_rule"), 900);
	}

	private function show_line_bot_edit_step_event(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("line_bot_event_options", $this->get_line_bot_event_options());
		$ctl->show_multi_dialog("wizard", "line_bot_edit_step_event.tpl", $ctl->t("wizard.line_bot.step_edit_event"), 900);
	}

	private function show_line_bot_edit_step_keyword(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "line_bot_edit_step_keyword.tpl", $ctl->t("wizard.line_bot.step_edit_event"), 900);
	}

	private function show_line_bot_edit_step_request(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->assign("line_bot_event_options", $this->get_line_bot_event_options());
		$ctl->assign("line_bot_keyword_preview", $this->build_line_bot_keyword_preview($state));
		$ctl->show_multi_dialog("wizard", "line_bot_edit_step_request.tpl", $ctl->t("wizard.line_bot.step_edit_request"), 900);
	}

	private function show_table_change_screen_done(Controller $ctl, array $state) {
		$ctl->assign("row", $state);
		$ctl->show_multi_dialog("wizard", "table_change_screen_done.tpl", $ctl->t("wizard.table_change.step_screen_done"), 760);
	}

	private function get_table_create_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_table_create_state");
		if (!is_array($state)) {
			$state = [];
		}
		$manual_fields_text = (string) ($state["manual_fields_text"] ?? "");
		$field_mode = (string) ($state["field_mode"] ?? "auto");
		if ($field_mode !== "manual") {
			$field_mode = "auto";
		}
		$create_mode = (string) ($state["create_mode"] ?? "normal");
		if ($create_mode !== "child") {
			$create_mode = "normal";
		}
		return [
			"project_name" => $this->detect_current_project_name(),
			"purpose" => (string) ($state["purpose"] ?? ""),
			"note_title" => trim((string) ($state["note_title"] ?? "")),
			"menu_name" => trim((string) ($state["menu_name"] ?? "")),
			"field_mode" => $field_mode,
			"manual_fields_text" => $this->normalize_fields_text_for_human($manual_fields_text),
			"field_mode_label" => $field_mode === "manual" ? $ctl->t("wizard.field_mode.manual") : $ctl->t("wizard.field_mode.auto"),
			"create_mode" => $create_mode,
			"create_mode_label" => $create_mode === "child" ? $ctl->t("wizard.create_mode.child") : $ctl->t("wizard.create_mode.normal"),
			"parent_tb_name" => $this->normalize_table_name((string) ($state["parent_tb_name"] ?? "")),
			"parent_db_id" => $this->normalize_single_id($state["parent_db_id"] ?? ""),
			"parent_menu_name" => trim((string) ($state["parent_menu_name"] ?? ""))
		];
	}

	private function get_note_edit_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_note_edit_state");
		if (!is_array($state)) {
			$state = [];
		}
		$show_menu = (string) ($state["show_menu"] ?? "1");
		if (!isset($this->get_note_show_menu_options()[$show_menu])) {
			$show_menu = "1";
		}
		$sort_order = (string) ($state["sort_order"] ?? "4");
		if (!isset($this->get_note_sort_order_options()[$sort_order])) {
			$sort_order = "4";
		}
		$edit_width = trim((string) ($state["edit_width"] ?? "800"));
		if ($edit_width === "") {
			$edit_width = "800";
		}
		$list_type = (string) ($state["list_type"] ?? "0");
		if (!isset($this->get_note_list_type_options()[$list_type])) {
			$list_type = "0";
		}
		$show_duplicate = (string) ($state["show_duplicate"] ?? "0");
		if (!isset($this->get_note_toggle_options()[$show_duplicate])) {
			$show_duplicate = "0";
		}
		$show_id = (string) ($state["show_id"] ?? "0");
		if (!isset($this->get_note_toggle_options()[$show_id])) {
			$show_id = "0";
		}
		$side_list_type = (string) ($state["side_list_type"] ?? "0");
		if (!isset($this->get_note_side_list_type_options()[$side_list_type])) {
			$side_list_type = "0";
		}
		$cascade_delete_flag = (string) ($state["cascade_delete_flag"] ?? "0");
		if (!isset($this->get_cascade_delete_flag_options()[$cascade_delete_flag])) {
			$cascade_delete_flag = "0";
		}
		$show_icon_on_parent_list = (string) ($state["show_icon_on_parent_list"] ?? "0");
		if (!isset($this->get_note_parent_icon_options()[$show_icon_on_parent_list])) {
			$show_icon_on_parent_list = "0";
		}
		$has_parent_note = ((int) ($state["has_parent_note"] ?? 0)) > 0 ? 1 : 0;
		return [
			"target_tb_name" => $this->normalize_table_name((string) ($state["target_tb_name"] ?? "")),
			"db_id" => $this->normalize_single_id($state["db_id"] ?? ""),
			"menu_name" => trim((string) ($state["menu_name"] ?? "")),
			"description" => trim((string) ($state["description"] ?? "")),
			"show_menu" => $show_menu,
			"show_menu_label" => $this->get_note_show_menu_options()[$show_menu],
			"sortkey" => trim((string) ($state["sortkey"] ?? "id")),
			"sort_order" => $sort_order,
			"sort_order_label" => $this->get_note_sort_order_options()[$sort_order],
			"edit_width" => $edit_width,
			"list_type" => $list_type,
			"list_type_label" => $this->get_note_list_type_options()[$list_type],
			"show_duplicate" => $show_duplicate,
			"show_duplicate_label" => $this->get_note_toggle_options()[$show_duplicate],
			"show_id" => $show_id,
			"show_id_label" => $this->get_note_toggle_options()[$show_id],
			"side_list_type" => $side_list_type,
			"side_list_type_label" => $this->get_note_side_list_type_options()[$side_list_type],
			"cascade_delete_flag" => $cascade_delete_flag,
			"cascade_delete_flag_label" => $this->get_cascade_delete_flag_options()[$cascade_delete_flag],
			"show_icon_on_parent_list" => $show_icon_on_parent_list,
			"show_icon_on_parent_list_label" => $this->get_note_parent_icon_options()[$show_icon_on_parent_list],
			"has_parent_note" => $has_parent_note
		];
	}

	private function get_note_delete_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_note_delete_state");
		if (!is_array($state)) {
			$state = [];
		}
		return [
			"target_tb_name" => $this->normalize_table_name((string) ($state["target_tb_name"] ?? "")),
			"db_id" => $this->normalize_single_id($state["db_id"] ?? ""),
			"menu_name" => trim((string) ($state["menu_name"] ?? "")),
			"description" => trim((string) ($state["description"] ?? ""))
		];
	}

	private function get_parent_child_note_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_parent_child_note_state");
		if (!is_array($state)) {
			$state = [];
		}
		$display_type = trim((string) ($state["dropdown_item_display_type"] ?? "field"));
		if (!isset($this->get_dropdown_item_display_type_options()[$display_type])) {
			$display_type = "field";
		}
		$cascade = (string) ($state["cascade_delete_flag"] ?? "0");
		if (!isset($this->get_cascade_delete_flag_options()[$cascade])) {
			$cascade = "0";
		}
		return [
			"child_tb_name" => $this->normalize_table_name((string) ($state["child_tb_name"] ?? "")),
			"child_db_id" => $this->normalize_single_id($state["child_db_id"] ?? ""),
			"child_menu_name" => trim((string) ($state["child_menu_name"] ?? "")),
			"parent_tb_name" => $this->normalize_table_name((string) ($state["parent_tb_name"] ?? "")),
			"parent_db_id" => $this->normalize_single_id($state["parent_db_id"] ?? ""),
			"parent_menu_name" => trim((string) ($state["parent_menu_name"] ?? "")),
			"dropdown_item" => trim((string) ($state["dropdown_item"] ?? "id")),
			"dropdown_item_display_type" => $display_type,
			"dropdown_item_display_type_label" => $this->get_dropdown_item_display_type_options()[$display_type],
			"dropdown_item_template" => trim((string) ($state["dropdown_item_template"] ?? "")),
			"list_width" => trim((string) ($state["list_width"] ?? "800")),
			"cascade_delete_flag" => $cascade,
			"cascade_delete_flag_label" => $this->get_cascade_delete_flag_options()[$cascade]
		];
	}

	private function get_original_form_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_original_form_state");
		if (!is_array($state)) {
			$state = [];
		}
		$db_id = $this->normalize_single_id($state["db_id"] ?? "");
		$tb_name = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		return [
			"db_id" => $db_id,
			"tb_name" => $tb_name,
			"parent_tb_id" => (int) ($state["parent_tb_id"] ?? 0),
			"place" => (string) ($state["place"] ?? ""),
			"button_title" => (string) ($state["button_title"] ?? ""),
			"request_text" => (string) ($state["request_text"] ?? "")
		];
	}

	private function get_db_additionals_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_db_additionals_state");
		if (!is_array($state)) {
			$state = [];
		}
		return [
			"additional_type" => (string) ($state["additional_type"] ?? "")
		];
	}

	private function get_db_additionals_edit_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_db_additionals_edit_state");
		if (!is_array($state)) {
			$state = [];
		}
		return [
			"additional_id" => $this->normalize_single_id($state["additional_id"] ?? ""),
			"tb_name" => $this->normalize_table_name((string) ($state["tb_name"] ?? "")),
			"note_name" => trim((string) ($state["note_name"] ?? "")),
			"button_title" => trim((string) ($state["button_title"] ?? "")),
			"class_name" => trim((string) ($state["class_name"] ?? "")),
			"function_name" => trim((string) ($state["function_name"] ?? "")),
			"place" => trim((string) ($state["place"] ?? "")),
			"request_text" => (string) ($state["request_text"] ?? "")
		];
	}

	private function get_db_additionals_delete_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_db_additionals_delete_state");
		if (!is_array($state)) {
			$state = [];
		}
		return [
			"additional_id" => $this->normalize_single_id($state["additional_id"] ?? ""),
			"tb_name" => $this->normalize_table_name((string) ($state["tb_name"] ?? "")),
			"note_name" => trim((string) ($state["note_name"] ?? "")),
			"button_title" => trim((string) ($state["button_title"] ?? "")),
			"class_name" => trim((string) ($state["class_name"] ?? "")),
			"function_name" => trim((string) ($state["function_name"] ?? "")),
			"place" => trim((string) ($state["place"] ?? ""))
		];
	}

	private function get_db_additionals_sort_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_db_additionals_sort_state");
		if (!is_array($state)) {
			$state = [];
		}
		return [
			"db_id" => $this->normalize_single_id($state["db_id"] ?? ""),
			"tb_name" => $this->normalize_table_name((string) ($state["tb_name"] ?? "")),
			"parent_tb_id" => (int) ($state["parent_tb_id"] ?? 0),
			"place" => trim((string) ($state["place"] ?? ""))
		];
	}

	private function get_pdf_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_pdf_state");
		if (!is_array($state)) {
			$state = [];
		}
		$db_id = $this->normalize_single_id($state["db_id"] ?? "");
		$tb_name = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		return [
			"db_id" => $db_id,
			"tb_name" => $tb_name,
			"parent_tb_id" => (int) ($state["parent_tb_id"] ?? 0),
			"place" => (string) ($state["place"] ?? ""),
			"field_ids" => $this->normalize_id_list($state["field_ids"] ?? null),
			"button_title" => (string) ($state["button_title"] ?? ""),
			"request_text" => (string) ($state["request_text"] ?? "")
		];
	}

	private function get_csv_download_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_csv_download_state");
		if (!is_array($state)) {
			$state = [];
		}
		$db_id = $this->normalize_single_id($state["db_id"] ?? "");
		$tb_name = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		return [
			"db_id" => $db_id,
			"tb_name" => $tb_name,
			"parent_tb_id" => (int) ($state["parent_tb_id"] ?? 0),
			"place" => (string) ($state["place"] ?? ""),
			"field_ids" => $this->normalize_id_list($state["field_ids"] ?? null),
			"button_title" => (string) ($state["button_title"] ?? ""),
			"request_text" => (string) ($state["request_text"] ?? "")
		];
	}

	private function get_csv_upload_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_csv_upload_state");
		if (!is_array($state)) {
			$state = [];
		}
		$db_id = $this->normalize_single_id($state["db_id"] ?? "");
		$tb_name = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		return [
			"db_id" => $db_id,
			"tb_name" => $tb_name,
			"parent_tb_id" => (int) ($state["parent_tb_id"] ?? 0),
			"place" => (string) ($state["place"] ?? ""),
			"field_ids" => $this->normalize_id_list($state["field_ids"] ?? null),
			"button_title" => (string) ($state["button_title"] ?? ""),
			"request_text" => (string) ($state["request_text"] ?? "")
		];
	}

	private function get_chart_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_chart_state");
		if (!is_array($state)) {
			$state = [];
		}
		$db_id = $this->normalize_single_id($state["db_id"] ?? "");
		$tb_name = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		return [
			"db_id" => $db_id,
			"tb_name" => $tb_name,
			"parent_tb_id" => (int) ($state["parent_tb_id"] ?? 0),
			"place" => (string) ($state["place"] ?? ""),
			"chart_type" => (string) ($state["chart_type"] ?? ""),
			"field_ids" => $this->normalize_id_list($state["field_ids"] ?? null),
			"button_title" => (string) ($state["button_title"] ?? ""),
			"request_text" => (string) ($state["request_text"] ?? "")
		];
	}

	private function get_line_message_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_line_message_state");
		if (!is_array($state)) {
			$state = [];
		}
		$db_id = $this->normalize_single_id($state["db_id"] ?? "");
		$tb_name = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		return [
			"db_id" => $db_id,
			"tb_name" => $tb_name,
			"parent_tb_id" => (int) ($state["parent_tb_id"] ?? 0),
			"place" => (string) ($state["place"] ?? ""),
			"button_title" => (string) ($state["button_title"] ?? ""),
			"request_text" => (string) ($state["request_text"] ?? "")
		];
	}

	private function get_cron_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_cron_state");
		if (!is_array($state)) {
			$state = [];
		}
		return [
			"cron_action" => (string) ($state["cron_action"] ?? ""),
			"cron_id" => $this->normalize_single_id($state["cron_id"] ?? ""),
			"timing_text" => (string) ($state["timing_text"] ?? ""),
			"request_text" => (string) ($state["request_text"] ?? ""),
			"title" => (string) ($state["title"] ?? ""),
			"class_name" => (string) ($state["class_name"] ?? ""),
			"function_name" => (string) ($state["function_name"] ?? ""),
			"min" => $this->normalize_cron_component_list($state["min"] ?? null),
			"hour" => $this->normalize_cron_component_list($state["hour"] ?? null),
			"day" => $this->normalize_cron_component_list($state["day"] ?? null),
			"month" => $this->normalize_cron_component_list($state["month"] ?? null),
			"weekday" => $this->normalize_cron_component_list($state["weekday"] ?? null),
			"summary" => (string) ($state["summary"] ?? "")
		];
	}

	private function get_public_pages_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_public_pages_state");
		if (!is_array($state)) {
			$state = [];
		}
		return [
			"page_action" => (string) ($state["page_action"] ?? ""),
			"registry_id" => $this->normalize_single_id($state["registry_id"] ?? ""),
			"title" => (string) ($state["title"] ?? ""),
			"function_name" => (string) ($state["function_name"] ?? ""),
			"template_name" => (string) ($state["template_name"] ?? ""),
			"public_asset_ids" => $this->normalize_id_list($state["public_asset_ids"] ?? null),
			"public_asset_text" => (string) ($state["public_asset_text"] ?? ""),
			"request_text" => (string) ($state["request_text"] ?? ""),
			"common_header_text" => (string) ($state["common_header_text"] ?? ""),
			"common_footer_text" => (string) ($state["common_footer_text"] ?? ""),
			"common_nav_text" => (string) ($state["common_nav_text"] ?? ""),
			"common_style_text" => (string) ($state["common_style_text"] ?? "")
		];
	}

	private function get_line_bot_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_line_bot_state");
		if (!is_array($state)) {
			$state = [];
		}
		return [
			"line_action" => (string) ($state["line_action"] ?? ""),
			"event_type" => (string) ($state["event_type"] ?? ""),
			"keyword" => (string) ($state["keyword"] ?? ""),
			"button_title" => (string) ($state["button_title"] ?? ""),
			"request_text" => (string) ($state["request_text"] ?? ""),
			"line_channel_secret" => (string) ($state["line_channel_secret"] ?? ""),
			"line_accesstoken" => (string) ($state["line_accesstoken"] ?? ""),
			"line_forward_unknown_to_manager" => (string) ($state["line_forward_unknown_to_manager"] ?? "0"),
			"line_channel_secret_saved" => (string) ($state["line_channel_secret_saved"] ?? "0"),
			"line_accesstoken_saved" => (string) ($state["line_accesstoken_saved"] ?? "0")
		];
	}

	private function get_embed_app_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_embed_app_state");
		if (!is_array($state)) {
			$state = [];
		}
		return [
			"embed_action" => (string) ($state["embed_action"] ?? ""),
			"embed_app_id" => $this->normalize_single_id($state["embed_app_id"] ?? ""),
			"embed_key" => (string) ($state["embed_key"] ?? ""),
			"title" => (string) ($state["title"] ?? ""),
			"class_name" => $this->normalize_embed_app_class_name((string) ($state["class_name"] ?? "")),
			"request_text" => (string) ($state["request_text"] ?? ""),
			"snippet_code" => (string) ($state["snippet_code"] ?? "")
		];
	}

	private function get_dashboard_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_dashboard_state");
		if (!is_array($state)) {
			$state = [];
		}
		return [
			"dashboard_action" => (string) ($state["dashboard_action"] ?? ""),
			"dashboard_id" => $this->normalize_single_id($state["dashboard_id"] ?? ""),
			"title" => (string) ($state["title"] ?? ""),
			"class_name" => trim((string) ($state["class_name"] ?? "")),
			"function_name" => trim((string) ($state["function_name"] ?? "dashboard")),
			"column_width" => (string) ((int) ($state["column_width"] ?? 1)),
			"request_text" => (string) ($state["request_text"] ?? "")
		];
	}

	private function get_line_member_link_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_line_member_link_state");
		if (!is_array($state)) {
			$state = [];
		}
		return [
			"db_id" => $this->normalize_single_id($state["db_id"] ?? ""),
			"tb_name" => $this->normalize_table_name((string) ($state["tb_name"] ?? "")),
			"user_id_field" => (string) ($state["user_id_field"] ?? ""),
			"display_name_field" => (string) ($state["display_name_field"] ?? ""),
			"name_field" => (string) ($state["name_field"] ?? ""),
			"create_if_missing" => (string) ($state["create_if_missing"] ?? "1"),
			"button_title" => (string) ($state["button_title"] ?? ""),
			"request_text" => (string) ($state["request_text"] ?? "")
		];
	}

	private function get_line_bot_edit_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_line_bot_edit_state");
		if (!is_array($state)) {
			$state = [];
		}
		return [
			"rule_id" => $this->normalize_single_id($state["rule_id"] ?? ""),
			"event_type" => (string) ($state["event_type"] ?? ""),
			"keyword" => (string) ($state["keyword"] ?? ""),
			"action_class" => (string) ($state["action_class"] ?? ""),
			"button_title" => (string) ($state["button_title"] ?? ""),
			"request_text" => (string) ($state["request_text"] ?? "")
		];
	}

	private function get_line_bot_delete_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_line_bot_delete_state");
		if (!is_array($state)) {
			$state = [];
		}
		return [
			"rule_id" => $this->normalize_single_id($state["rule_id"] ?? ""),
			"event_type" => (string) ($state["event_type"] ?? ""),
			"keyword" => (string) ($state["keyword"] ?? ""),
			"action_class" => (string) ($state["action_class"] ?? "")
		];
	}

	private function save_original_form_state(Controller $ctl, array $state): void {
		$state["db_id"] = $this->normalize_single_id($state["db_id"] ?? "");
		$state["tb_name"] = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		$state["parent_tb_id"] = (int) ($state["parent_tb_id"] ?? 0);
		$state["place"] = (string) ($state["place"] ?? "");
		$state["button_title"] = trim((string) ($state["button_title"] ?? ""));
		$state["request_text"] = (string) ($state["request_text"] ?? "");
		$ctl->set_session("wizard_original_form_state", $state);
	}

	private function save_db_additionals_state(Controller $ctl, array $state): void {
		$state["additional_type"] = (string) ($state["additional_type"] ?? "");
		$ctl->set_session("wizard_db_additionals_state", $state);
	}

	private function save_db_additionals_edit_state(Controller $ctl, array $state): void {
		$state["additional_id"] = $this->normalize_single_id($state["additional_id"] ?? "");
		$state["tb_name"] = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		$state["note_name"] = trim((string) ($state["note_name"] ?? ""));
		$state["button_title"] = trim((string) ($state["button_title"] ?? ""));
		$state["class_name"] = trim((string) ($state["class_name"] ?? ""));
		$state["function_name"] = trim((string) ($state["function_name"] ?? ""));
		$state["place"] = trim((string) ($state["place"] ?? ""));
		$state["request_text"] = (string) ($state["request_text"] ?? "");
		$ctl->set_session("wizard_db_additionals_edit_state", $state);
	}

	private function save_db_additionals_delete_state(Controller $ctl, array $state): void {
		$state["additional_id"] = $this->normalize_single_id($state["additional_id"] ?? "");
		$state["tb_name"] = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		$state["note_name"] = trim((string) ($state["note_name"] ?? ""));
		$state["button_title"] = trim((string) ($state["button_title"] ?? ""));
		$state["class_name"] = trim((string) ($state["class_name"] ?? ""));
		$state["function_name"] = trim((string) ($state["function_name"] ?? ""));
		$state["place"] = trim((string) ($state["place"] ?? ""));
		$ctl->set_session("wizard_db_additionals_delete_state", $state);
	}

	private function save_db_additionals_sort_state(Controller $ctl, array $state): void {
		$state["db_id"] = $this->normalize_single_id($state["db_id"] ?? "");
		$state["tb_name"] = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		$state["parent_tb_id"] = (int) ($state["parent_tb_id"] ?? 0);
		$state["place"] = trim((string) ($state["place"] ?? ""));
		$ctl->set_session("wizard_db_additionals_sort_state", $state);
	}

	private function save_pdf_state(Controller $ctl, array $state): void {
		$state["db_id"] = $this->normalize_single_id($state["db_id"] ?? "");
		$state["tb_name"] = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		$state["parent_tb_id"] = (int) ($state["parent_tb_id"] ?? 0);
		$state["place"] = (string) ($state["place"] ?? "");
		$state["field_ids"] = $this->normalize_id_list($state["field_ids"] ?? null);
		$state["button_title"] = trim((string) ($state["button_title"] ?? ""));
		$state["request_text"] = (string) ($state["request_text"] ?? "");
		$ctl->set_session("wizard_pdf_state", $state);
	}

	private function save_csv_download_state(Controller $ctl, array $state): void {
		$state["db_id"] = $this->normalize_single_id($state["db_id"] ?? "");
		$state["tb_name"] = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		$state["parent_tb_id"] = (int) ($state["parent_tb_id"] ?? 0);
		$state["place"] = (string) ($state["place"] ?? "");
		$state["field_ids"] = $this->normalize_id_list($state["field_ids"] ?? null);
		$state["button_title"] = trim((string) ($state["button_title"] ?? ""));
		$state["request_text"] = (string) ($state["request_text"] ?? "");
		$ctl->set_session("wizard_csv_download_state", $state);
	}

	private function save_csv_upload_state(Controller $ctl, array $state): void {
		$state["db_id"] = $this->normalize_single_id($state["db_id"] ?? "");
		$state["tb_name"] = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		$state["parent_tb_id"] = (int) ($state["parent_tb_id"] ?? 0);
		$state["place"] = (string) ($state["place"] ?? "");
		$state["field_ids"] = $this->normalize_id_list($state["field_ids"] ?? null);
		$state["button_title"] = trim((string) ($state["button_title"] ?? ""));
		$state["request_text"] = (string) ($state["request_text"] ?? "");
		$ctl->set_session("wizard_csv_upload_state", $state);
	}

	private function save_chart_state(Controller $ctl, array $state): void {
		$state["db_id"] = $this->normalize_single_id($state["db_id"] ?? "");
		$state["tb_name"] = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		$state["parent_tb_id"] = (int) ($state["parent_tb_id"] ?? 0);
		$state["place"] = (string) ($state["place"] ?? "");
		$state["chart_type"] = trim((string) ($state["chart_type"] ?? ""));
		$state["field_ids"] = $this->normalize_id_list($state["field_ids"] ?? null);
		$state["button_title"] = trim((string) ($state["button_title"] ?? ""));
		$state["request_text"] = (string) ($state["request_text"] ?? "");
		$ctl->set_session("wizard_chart_state", $state);
	}

	private function save_line_message_state(Controller $ctl, array $state): void {
		$state["db_id"] = $this->normalize_single_id($state["db_id"] ?? "");
		$state["tb_name"] = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		$state["parent_tb_id"] = (int) ($state["parent_tb_id"] ?? 0);
		$state["place"] = (string) ($state["place"] ?? "");
		$state["button_title"] = trim((string) ($state["button_title"] ?? ""));
		$state["request_text"] = (string) ($state["request_text"] ?? "");
		$ctl->set_session("wizard_line_message_state", $state);
	}

	private function save_cron_state(Controller $ctl, array $state): void {
		$state["cron_action"] = trim((string) ($state["cron_action"] ?? ""));
		$state["cron_id"] = $this->normalize_single_id($state["cron_id"] ?? "");
		$state["timing_text"] = (string) ($state["timing_text"] ?? "");
		$state["request_text"] = (string) ($state["request_text"] ?? "");
		$state["title"] = trim((string) ($state["title"] ?? ""));
		$state["class_name"] = trim((string) ($state["class_name"] ?? ""));
		$state["function_name"] = trim((string) ($state["function_name"] ?? ""));
		$state["min"] = $this->normalize_cron_component_list($state["min"] ?? null);
		$state["hour"] = $this->normalize_cron_component_list($state["hour"] ?? null);
		$state["day"] = $this->normalize_cron_component_list($state["day"] ?? null);
		$state["month"] = $this->normalize_cron_component_list($state["month"] ?? null);
		$state["weekday"] = $this->normalize_cron_component_list($state["weekday"] ?? null);
		$state["summary"] = (string) ($state["summary"] ?? "");
		$ctl->set_session("wizard_cron_state", $state);
	}

	private function save_public_pages_state(Controller $ctl, array $state): void {
		$state["page_action"] = trim((string) ($state["page_action"] ?? ""));
		$state["registry_id"] = $this->normalize_single_id($state["registry_id"] ?? "");
		$state["title"] = trim((string) ($state["title"] ?? ""));
		$state["function_name"] = $this->normalize_public_function_name((string) ($state["function_name"] ?? ""));
		$state["template_name"] = trim((string) ($state["template_name"] ?? ""));
		$state["public_asset_ids"] = $this->normalize_id_list($state["public_asset_ids"] ?? null);
		$state["public_asset_text"] = (string) ($state["public_asset_text"] ?? "");
		$state["request_text"] = (string) ($state["request_text"] ?? "");
		$state["common_header_text"] = trim((string) ($state["common_header_text"] ?? ""));
		$state["common_footer_text"] = trim((string) ($state["common_footer_text"] ?? ""));
		$state["common_nav_text"] = trim((string) ($state["common_nav_text"] ?? ""));
		$state["common_style_text"] = trim((string) ($state["common_style_text"] ?? ""));
		$ctl->set_session("wizard_public_pages_state", $state);
	}

	private function save_line_bot_state(Controller $ctl, array $state): void {
		$state["line_action"] = trim((string) ($state["line_action"] ?? ""));
		$state["event_type"] = trim((string) ($state["event_type"] ?? ""));
		$state["keyword"] = trim((string) ($state["keyword"] ?? ""));
		$state["button_title"] = trim((string) ($state["button_title"] ?? ""));
		$state["request_text"] = (string) ($state["request_text"] ?? "");
		$state["line_channel_secret"] = trim((string) ($state["line_channel_secret"] ?? ""));
		$state["line_accesstoken"] = trim((string) ($state["line_accesstoken"] ?? ""));
		$state["line_forward_unknown_to_manager"] = ((string) ($state["line_forward_unknown_to_manager"] ?? "0") === "1") ? "1" : "0";
		$state["line_channel_secret_saved"] = ((string) ($state["line_channel_secret_saved"] ?? "0") === "1") ? "1" : "0";
		$state["line_accesstoken_saved"] = ((string) ($state["line_accesstoken_saved"] ?? "0") === "1") ? "1" : "0";
		$ctl->set_session("wizard_line_bot_state", $state);
	}

	private function get_line_forward_unknown_to_manager_options(Controller $ctl): array {
		return [
			"0" => $ctl->t("setting.line_forward_unknown_to_manager.option.forward"),
			"1" => $ctl->t("setting.line_forward_unknown_to_manager.option.no_forward"),
		];
	}

	private function get_line_bot_setting_row(Controller $ctl): array {
		$setting = $ctl->db("setting", "setting")->get(1);
		if (!is_array($setting) || count($setting) === 0) {
			$setting = ["id" => 1];
		}
		return $setting;
	}

	private function save_embed_app_state(Controller $ctl, array $state): void {
		$state["embed_action"] = trim((string) ($state["embed_action"] ?? ""));
		$state["embed_app_id"] = $this->normalize_single_id($state["embed_app_id"] ?? "");
		$state["embed_key"] = trim((string) ($state["embed_key"] ?? ""));
		$state["title"] = trim((string) ($state["title"] ?? ""));
		$state["class_name"] = $this->normalize_embed_app_class_name((string) ($state["class_name"] ?? ""));
		$state["request_text"] = (string) ($state["request_text"] ?? "");
		$state["snippet_code"] = (string) ($state["snippet_code"] ?? "");
		$ctl->set_session("wizard_embed_app_state", $state);
	}

	private function save_dashboard_state(Controller $ctl, array $state): void {
		$state["dashboard_action"] = trim((string) ($state["dashboard_action"] ?? ""));
		$state["dashboard_id"] = $this->normalize_single_id($state["dashboard_id"] ?? "");
		$state["title"] = trim((string) ($state["title"] ?? ""));
		$state["class_name"] = trim((string) ($state["class_name"] ?? ""));
		$state["function_name"] = "dashboard";
		$state["column_width"] = (string) ((int) ($state["column_width"] ?? 1));
		$state["request_text"] = (string) ($state["request_text"] ?? "");
		$ctl->set_session("wizard_dashboard_state", $state);
	}

	private function get_embed_app_options(Controller $ctl): array {
		$opt = ["" => $ctl->t("wizard.select_placeholder")];
		$list = $ctl->db("embed_app", "embed_app")->getall("sort", SORT_ASC);
		if (!is_array($list)) {
			return $opt;
		}
		foreach ($list as $one) {
			$id = $this->normalize_single_id($one["id"] ?? "");
			$title = trim((string) ($one["title"] ?? ""));
			$class_name = trim((string) ($one["class_name"] ?? ""));
			$embed_key = trim((string) ($one["embed_key"] ?? ""));
			if ($id === "") {
				continue;
			}
			$label = $title !== "" ? $title : ("embed_app_" . $id);
			if ($class_name !== "") {
				$label .= " / " . $class_name;
			}
			if ($embed_key !== "" && $embed_key !== $class_name) {
				$label .= " / " . $embed_key;
			}
			$opt[$id] = $label;
		}
		return $opt;
	}

	private function get_dashboard_options(Controller $ctl): array {
		$opt = ["" => $ctl->t("wizard.select_placeholder")];
		$list = $ctl->db("dashboard", "dashboard")->getall("sort", SORT_ASC);
		if (!is_array($list)) {
			return $opt;
		}
		$width_opt = $this->get_dashboard_column_width_options();
		foreach ($list as $one) {
			$id = $this->normalize_single_id($one["id"] ?? "");
			$class_name = trim((string) ($one["class_name"] ?? ""));
			$function_name = trim((string) ($one["function_name"] ?? ""));
			$column_width = (int) ($one["column_width"] ?? 1);
			if ($id === "") {
				continue;
			}
			$label = $class_name . " / " . $function_name;
			$label .= " / " . ($width_opt[$column_width] ?? "1 column");
			$opt[$id] = $label;
		}
		return $opt;
	}

	private function get_embed_app_row(Controller $ctl, string $embed_app_id): ?array {
		$embed_app_id = $this->normalize_single_id($embed_app_id);
		if ($embed_app_id === "") {
			return null;
		}
		$data = $ctl->db("embed_app", "embed_app")->get((int) $embed_app_id);
		if (!is_array($data) || count($data) === 0) {
			return null;
		}
		return $data;
	}

	private function build_embed_app_snippet_code(Controller $ctl, string $embed_key): string {
		$embed_key = trim($embed_key);
		if ($embed_key === "") {
			return "";
		}
		$loader_url = $ctl->get_APP_URL("embed_app_runtime", "loader_js");
		$route_url = $ctl->get_APP_URL("embed_app_runtime", "route", ["embed_key" => $embed_key]);
		$target_id = "embed-app-" . preg_replace('/[^a-zA-Z0-9_-]/', '-', $embed_key);
		$loader_url = htmlspecialchars($loader_url, ENT_QUOTES, 'UTF-8');
		$route_url = htmlspecialchars($route_url, ENT_QUOTES, 'UTF-8');
		$embed_key = htmlspecialchars($embed_key, ENT_QUOTES, 'UTF-8');
		return '<div id="' . $target_id . '"></div>' . "
"
			. '<script src="' . $loader_url . '" data-target="#' . $target_id . '" data-boot-url="' . $route_url . '" data-embed-key="' . $embed_key . '" defer></script>';
	}

	private function get_dashboard_row(Controller $ctl, string $dashboard_id): ?array {
		$dashboard_id = $this->normalize_single_id($dashboard_id);
		if ($dashboard_id === "") {
			return null;
		}
		$data = $ctl->db("dashboard", "dashboard")->get((int) $dashboard_id);
		if (!is_array($data) || count($data) === 0) {
			return null;
		}
		return $data;
	}

	private function get_dashboard_column_width_options(): array {
		return [
			1 => "1 column",
			2 => "2 columns",
			3 => "3 columns"
		];
	}

	private function save_line_member_link_state(Controller $ctl, array $state): void {
		$state["db_id"] = $this->normalize_single_id($state["db_id"] ?? "");
		$state["tb_name"] = $this->normalize_table_name((string) ($state["tb_name"] ?? ""));
		$state["user_id_field"] = trim((string) ($state["user_id_field"] ?? ""));
		$state["display_name_field"] = trim((string) ($state["display_name_field"] ?? ""));
		$state["name_field"] = trim((string) ($state["name_field"] ?? ""));
		$state["create_if_missing"] = (string) ($state["create_if_missing"] ?? "1");
		$state["button_title"] = trim((string) ($state["button_title"] ?? ""));
		$state["request_text"] = (string) ($state["request_text"] ?? "");
		$ctl->set_session("wizard_line_member_link_state", $state);
	}

	private function save_line_bot_edit_state(Controller $ctl, array $state): void {
		$state["rule_id"] = $this->normalize_single_id($state["rule_id"] ?? "");
		$state["event_type"] = trim((string) ($state["event_type"] ?? ""));
		$state["keyword"] = trim((string) ($state["keyword"] ?? ""));
		$state["action_class"] = trim((string) ($state["action_class"] ?? ""));
		$state["button_title"] = trim((string) ($state["button_title"] ?? ""));
		$state["request_text"] = (string) ($state["request_text"] ?? "");
		$ctl->set_session("wizard_line_bot_edit_state", $state);
	}

	private function save_line_bot_delete_state(Controller $ctl, array $state): void {
		$state["rule_id"] = $this->normalize_single_id($state["rule_id"] ?? "");
		$state["event_type"] = trim((string) ($state["event_type"] ?? ""));
		$state["keyword"] = trim((string) ($state["keyword"] ?? ""));
		$state["action_class"] = trim((string) ($state["action_class"] ?? ""));
		$ctl->set_session("wizard_line_bot_delete_state", $state);
	}

	private function detect_current_project_name(): string {
		$project = basename(dirname(__DIR__, 3));
		if ($project === "" || $project === "fbp" || $project === "web") {
			return "app-framework5";
		}
		return $project;
	}

	private function save_table_create_state(Controller $ctl, array $state) {
		$state["project_name"] = $this->detect_current_project_name();
		$state["purpose"] = trim((string) ($state["purpose"] ?? ""));
		$state["note_title"] = trim((string) ($state["note_title"] ?? ""));
		$state["menu_name"] = trim((string) ($state["menu_name"] ?? ""));
		$state["field_mode"] = ((string) ($state["field_mode"] ?? "auto")) === "manual" ? "manual" : "auto";
		$state["manual_fields_text"] = $this->normalize_fields_text_for_human((string) ($state["manual_fields_text"] ?? ""));
		$state["create_mode"] = ((string) ($state["create_mode"] ?? "normal")) === "child" ? "child" : "normal";
		$state["parent_tb_name"] = $this->normalize_table_name((string) ($state["parent_tb_name"] ?? ""));
		$state["parent_db_id"] = $this->normalize_single_id($state["parent_db_id"] ?? "");
		$state["parent_menu_name"] = trim((string) ($state["parent_menu_name"] ?? ""));
		$ctl->set_session("wizard_table_create_state", $state);
	}

	private function save_note_edit_state(Controller $ctl, array $state) {
		$state["target_tb_name"] = $this->normalize_table_name((string) ($state["target_tb_name"] ?? ""));
		$state["db_id"] = $this->normalize_single_id($state["db_id"] ?? "");
		$state["menu_name"] = trim((string) ($state["menu_name"] ?? ""));
		$state["description"] = trim((string) ($state["description"] ?? ""));
		$state["show_menu"] = (string) ($state["show_menu"] ?? "1");
		$state["sortkey"] = trim((string) ($state["sortkey"] ?? "id"));
		$state["sort_order"] = (string) ($state["sort_order"] ?? "4");
		$state["edit_width"] = trim((string) ($state["edit_width"] ?? "800"));
		$state["list_type"] = (string) ($state["list_type"] ?? "0");
		$state["show_duplicate"] = (string) ($state["show_duplicate"] ?? "0");
		$state["show_id"] = (string) ($state["show_id"] ?? "0");
		$state["side_list_type"] = (string) ($state["side_list_type"] ?? "0");
		$state["cascade_delete_flag"] = (string) ($state["cascade_delete_flag"] ?? "0");
		$state["show_icon_on_parent_list"] = (string) ($state["show_icon_on_parent_list"] ?? "0");
		$state["has_parent_note"] = ((int) ($state["has_parent_note"] ?? 0)) > 0 ? 1 : 0;
		$ctl->set_session("wizard_note_edit_state", $state);
	}

	private function save_note_delete_state(Controller $ctl, array $state) {
		$state["target_tb_name"] = $this->normalize_table_name((string) ($state["target_tb_name"] ?? ""));
		$state["db_id"] = $this->normalize_single_id($state["db_id"] ?? "");
		$state["menu_name"] = trim((string) ($state["menu_name"] ?? ""));
		$state["description"] = trim((string) ($state["description"] ?? ""));
		$ctl->set_session("wizard_note_delete_state", $state);
	}

	private function save_parent_child_note_state(Controller $ctl, array $state) {
		$state["child_tb_name"] = $this->normalize_table_name((string) ($state["child_tb_name"] ?? ""));
		$state["child_db_id"] = $this->normalize_single_id($state["child_db_id"] ?? "");
		$state["child_menu_name"] = trim((string) ($state["child_menu_name"] ?? ""));
		$state["parent_tb_name"] = $this->normalize_table_name((string) ($state["parent_tb_name"] ?? ""));
		$state["parent_db_id"] = $this->normalize_single_id($state["parent_db_id"] ?? "");
		$state["parent_menu_name"] = trim((string) ($state["parent_menu_name"] ?? ""));
		$state["dropdown_item"] = trim((string) ($state["dropdown_item"] ?? "id"));
		$state["dropdown_item_display_type"] = trim((string) ($state["dropdown_item_display_type"] ?? "field"));
		$state["dropdown_item_template"] = trim((string) ($state["dropdown_item_template"] ?? ""));
		$state["list_width"] = trim((string) ($state["list_width"] ?? "800"));
		$state["cascade_delete_flag"] = (string) ($state["cascade_delete_flag"] ?? "0");
		$ctl->set_session("wizard_parent_child_note_state", $state);
	}

	private function build_field_candidate_lines_from_llm($candidates): string {
		if (!is_array($candidates)) {
			return "";
		}
		$allow_types = [
			"text" => 1,
			"textarea" => 1,
			"number" => 1,
			"date" => 1,
			"select" => 1,
			"email" => 1,
			"tel" => 1
		];
		$res = [];
		foreach ($candidates as $one) {
			if (is_string($one)) {
				$line = trim($one);
				if ($line !== "") {
					$res[] = $line;
				}
				continue;
			}
			if (!is_array($one)) {
				continue;
			}
			$title = trim((string) ($one["title"] ?? ""));
			$type = strtolower(trim((string) ($one["type"] ?? "text")));
			$note = trim((string) ($one["note"] ?? ""));
			if ($title === "") {
				continue;
			}
			if (!isset($allow_types[$type])) {
				$type = "text";
			}
			$line = $title . "（" . $type;
			if ($note !== "") {
				$line .= " / " . $note;
			}
			$line .= "）";
			$res[] = $line;
		}
		return implode("\n", $res);
	}

	private function decode_json_object(string $raw) {
		$src = trim($raw);
		if ($src === "") {
			return null;
		}
		$decoded = json_decode($src, true);
		if (is_array($decoded)) {
			return $decoded;
		}
		if (preg_match('/```(?:json)?\s*(\{[\s\S]*\})\s*```/u', $src, $m)) {
			$decoded = json_decode($m[1], true);
			if (is_array($decoded)) {
				return $decoded;
			}
		}
		if (preg_match('/(\{[\s\S]*\})/u', $src, $m2)) {
			$decoded = json_decode($m2[1], true);
			if (is_array($decoded)) {
				return $decoded;
			}
		}
		return null;
	}

	private function normalize_cron_component_list($val): array {
		if (!is_array($val)) {
			return [];
		}
		$res = [];
		foreach ($val as $one) {
			$s = trim((string) $one);
			if ($s === "") {
				continue;
			}
			$res[] = $s;
		}
		return array_values(array_unique($res));
	}

	private function build_cron_component_text(array $items, string $wildcard = "*"): string {
		if (count($items) === 0) {
			return $wildcard;
		}
		return implode(",", $items);
	}

	private function get_cron_options(Controller $ctl): array {
		$opt = ["" => $ctl->t("wizard.select_placeholder")];
		$list = $ctl->db("cron", "cron")->getall("sort", SORT_ASC);
		if (!is_array($list)) {
			return $opt;
		}
		foreach ($list as $one) {
			$id = $this->normalize_single_id($one["id"] ?? "");
			if ($id === "") {
				continue;
			}
			$title = trim((string) ($one["title"] ?? ""));
			$class_name = trim((string) ($one["class_name"] ?? ""));
			$function_name = trim((string) ($one["function_name"] ?? ""));
			$label = $title !== "" ? $title : ("cron_" . $id);
			if ($class_name !== "" || $function_name !== "") {
				$label .= " / " . $class_name . "::" . $function_name;
			}
			$opt[$id] = $label;
		}
		return $opt;
	}

	private function get_cron_editable_row(Controller $ctl, string $cron_id): ?array {
		$cron_id = $this->normalize_single_id($cron_id);
		if ($cron_id === "") {
			return null;
		}
		$data = $ctl->db("cron", "cron")->get((int) $cron_id);
		if (!is_array($data) || count($data) === 0) {
			return null;
		}
		return $data;
	}

	private function get_cron_list_rows(Controller $ctl): array {
		$list = $ctl->db("cron", "cron")->getall("sort", SORT_ASC);
		if (!is_array($list)) {
			return [];
		}
		$rows = [];
		foreach ($list as $one) {
			$id = $this->normalize_single_id($one["id"] ?? "");
			if ($id === "") {
				continue;
			}
			$state = [
				"min" => $this->normalize_cron_component_list($one["min"] ?? null),
				"hour" => $this->normalize_cron_component_list($one["hour"] ?? null),
				"day" => $this->normalize_cron_component_list($one["day"] ?? null),
				"month" => $this->normalize_cron_component_list($one["month"] ?? null),
				"weekday" => $this->normalize_cron_component_list($one["weekday"] ?? null)
			];
			$rows[] = [
				"id" => $id,
				"title" => trim((string) ($one["title"] ?? "")),
				"class_name" => trim((string) ($one["class_name"] ?? "")),
				"function_name" => trim((string) ($one["function_name"] ?? "")),
				"timing_text" => $this->build_cron_human_timing_text($state)
			];
		}
		return $rows;
	}

	private function get_public_pages_registry_options(Controller $ctl): array {
		$opt = ["" => $ctl->t("wizard.select_placeholder")];
		$list = $ctl->db("public_pages_registry", "public_pages_registry")->getall("sort", SORT_ASC);
		if (!is_array($list)) {
			return $opt;
		}
		foreach ($list as $one) {
			$id = $this->normalize_single_id($one["id"] ?? "");
			$function_name = trim((string) ($one["function_name"] ?? ""));
			if ($id === "" || $function_name === "") {
				continue;
			}
			$title = trim((string) ($one["title"] ?? ""));
			$label = $title !== "" ? ($title . " (" . $function_name . ")") : $function_name;
			$opt[$id] = $label;
		}
		return $opt;
	}

	private function get_public_pages_menu_manage_rows(Controller $ctl): array {
		$list = $ctl->db("public_pages_registry", "public_pages_registry")->getall("menu_sort", SORT_ASC);
		if (!is_array($list)) {
			return [];
		}
		$res = [];
		foreach ($list as $row) {
			$id = $this->normalize_single_id($row["id"] ?? "");
			if ($id === "") {
				continue;
			}
			$res[] = [
				"id" => $id,
				"title" => trim((string) ($row["title"] ?? "")),
				"show_in_menu" => ((int) ($row["show_in_menu"] ?? 0) === 1) ? 1 : 0,
				"menu_label" => trim((string) ($row["menu_label"] ?? "")),
			];
		}
		return $res;
	}

	private function get_public_pages_registry_row(Controller $ctl, string $registry_id): ?array {
		$registry_id = $this->normalize_single_id($registry_id);
		if ($registry_id === "") {
			return null;
		}
		$data = $ctl->db("public_pages_registry", "public_pages_registry")->get((int) $registry_id);
		if (!is_array($data) || count($data) === 0) {
			return null;
		}
		return $data;
	}

	private function find_public_pages_registry_by_function_name(Controller $ctl, string $function_name): ?array {
		$function_name = $this->normalize_public_function_name($function_name);
		if ($function_name === "") {
			return null;
		}
		$list = $ctl->db("public_pages_registry", "public_pages_registry")->select("function_name", $function_name);
		if (!is_array($list) || count($list) === 0) {
			return null;
		}
		return $list[0];
	}

	private function get_public_asset_rows_for_wizard(Controller $ctl, array $selected_ids): array {
		$list = $ctl->db("public_assets", "public_assets")->getall("sort", SORT_ASC);
		if (!is_array($list)) {
			return [];
		}
		$selected_map = [];
		foreach ($selected_ids as $one) {
			$id = $this->normalize_single_id($one);
			if ($id === "") {
				continue;
			}
			$selected_map[$id] = 1;
		}
		$rows = [];
		foreach ($list as $one) {
			$id = $this->normalize_single_id($one["id"] ?? "");
			$asset_key = trim((string) ($one["asset_key"] ?? ""));
			if ($id === "" || $asset_key === "") {
				continue;
			}
			$one["selected"] = isset($selected_map[$id]) ? 1 : 0;
			$one["preview_url"] = $ctl->get_APP_URL("public_asset_media", "view", ["key" => $asset_key]);
			$rows[] = $one;
		}
		return $rows;
	}

	private function build_public_asset_prompt_text(Controller $ctl, array $selected_ids): string {
		$rows = $this->get_public_asset_rows_for_wizard($ctl, $selected_ids);
		if (count($rows) === 0) {
			return "";
		}
		$lines = [];
		foreach ($rows as $row) {
			if ((int) ($row["selected"] ?? 0) !== 1) {
				continue;
			}
			$asset_key = trim((string) ($row["asset_key"] ?? ""));
			if ($asset_key === "") {
				continue;
			}
			$original_filename = trim((string) ($row["original_filename"] ?? ""));
			$stored_filename = trim((string) ($row["stored_filename"] ?? ""));
			$line = "- " . $asset_key;
			if ($original_filename !== "") {
				$line .= " / " . $original_filename;
			}
			if ($stored_filename !== "") {
				$line .= " / " . $stored_filename;
			}
			$lines[] = $line;
		}
		return implode("\n", $lines);
	}

	private function build_selected_public_asset_rows(Controller $ctl, array $selected_ids): array {
		$rows = $this->get_public_asset_rows_for_wizard($ctl, $selected_ids);
		$res = [];
		foreach ($rows as $row) {
			if ((int) ($row["selected"] ?? 0) !== 1) {
				continue;
			}
			$res[] = $row;
		}
		return $res;
	}

	private function build_cron_human_timing_text(array $state): string {
		$min = $this->build_cron_component_text($this->normalize_cron_component_list($state["min"] ?? null));
		$hour = $this->build_cron_component_text($this->normalize_cron_component_list($state["hour"] ?? null));
		$day = $this->build_cron_component_text($this->normalize_cron_component_list($state["day"] ?? null));
		$month = $this->build_cron_component_text($this->normalize_cron_component_list($state["month"] ?? null));
		$weekday = $this->build_cron_component_text($this->normalize_cron_component_list($state["weekday"] ?? null));
		return "min=" . $min . ", hour=" . $hour . ", day=" . $day . ", month=" . $month . ", weekday=" . $weekday;
	}

	private function normalize_table_name(string $name): string {
		$src = strtolower(trim($name));
		$src = preg_replace('/[^a-z0-9_]+/', '_', $src);
		$src = trim((string) $src, '_');
		if ($src === '') {
			return '';
		}
		if (preg_match('/^[0-9]/', $src)) {
			$src = 't_' . $src;
		}
		return $src;
	}

	private function normalize_public_function_name(string $name): string {
		$src = trim($name);
		$src = preg_replace('/[^A-Za-z0-9_]+/', '_', $src);
		$src = trim((string) $src, '_');
		if ($src === '') {
			return '';
		}
		if (preg_match('/^[0-9]/', $src)) {
			$src = 'page_' . $src;
		}
		return strtolower($src);
	}

	private function normalize_embed_app_class_name(string $name): string {
		$src = trim($name);
		$src = preg_replace('/[^A-Za-z0-9_]+/', '_', $src);
		$src = trim((string) $src, '_');
		if ($src === '') {
			return '';
		}
		if (preg_match('/^[0-9]/', $src)) {
			$src = 'embed_app_' . $src;
		}
		return strtolower($src);
	}

	private function suggest_embed_app_class_name(string $title): string {
		$base = $this->normalize_embed_app_class_name($title);
		if (strpos($base, 'embed_app_') !== 0) {
			$base = 'embed_app_' . $base;
		}
		return $this->normalize_embed_app_class_name($base);
	}

	private function suggest_dashboard_class_name(string $title): string {
		$base = $this->normalize_embed_app_class_name($title);
		if (strpos($base, 'dashboard_') !== 0) {
			$base = 'dashboard_' . $base;
		}
		return $this->normalize_embed_app_class_name($base);
	}

	private function suggest_public_function_name(string $title): string {
		$suggest = $this->normalize_public_function_name($title);
		if ($suggest !== '') {
			return $suggest;
		}
		return 'public_page';
	}

	private function suggest_unique_public_function_name(Controller $ctl, string $title): string {
		$base = $this->suggest_public_function_name($title);
		if ($base === '') {
			return '';
		}
		if ($this->find_public_pages_registry_by_function_name($ctl, $base) === null) {
			return $base;
		}
		for ($i = 2; $i <= 999; $i++) {
			$candidate = $base . '_' . $i;
			if ($this->find_public_pages_registry_by_function_name($ctl, $candidate) === null) {
				return $candidate;
			}
		}
		return '';
	}

	private function build_public_pages_template_name(string $function_name): string {
		$function_name = $this->normalize_public_function_name($function_name);
		if ($function_name === '') {
			return '';
		}
		return $function_name . '.tpl';
	}

	private function build_table_create_plan_lines($row) {
		$lines = [];
		if ((string) ($row["create_mode"] ?? "") === "child") {
			$lines[] = "1. 親ノート: " . (string) ($row["parent_tb_name"] ?? "");
			$lines[] = "2. 用途とノート名から内部db名を決定";
			$lines[] = "3. parent_tb_id を設定して子ノートとして db を追加";
			$lines[] = "4. db_fields を追加し、必要な parent_id 連携を確認";
			$lines[] = "5. 標準画面（screen_fields）の初期表示を Codex が決定して設定";
			$lines[] = "6. app_call / data_get または data_list / app_checkで検証";
		} else {
			$lines[] = "1. 用途とノート名から内部db名を決定";
			$lines[] = "2. dbを追加し、左メニューから遷移できるよう設定";
			$lines[] = "3. db_fieldsを追加（項目は自動提案または手動指定を採用）";
			$lines[] = "4. 標準画面（screen_fields）の初期表示をCodexが決定して設定";
			$lines[] = "5. app_call / data_get または data_list / app_checkで検証";
		}
		if ((string) ($row["field_mode"] ?? "") === "manual") {
			$field_lines = $this->normalize_field_lines((string) ($row["manual_fields_text"] ?? ""));
			$lines[] = ((string) ($row["create_mode"] ?? "") === "child") ? "7. 手動指定の項目" : "6. 手動指定の項目";
			foreach ($field_lines as $one) {
				$lines[] = "   - " . $one;
			}
		} else {
			$lines[] = ((string) ($row["create_mode"] ?? "") === "child") ? "7. 項目はCodexに任せる" : "6. 項目はCodexに任せる";
		}
		return $lines;
	}

	private function build_table_create_prompt_text($row, $plan_lines) {
		$field_block = "- 項目は要件に応じて Codex 側で提案・実装してください。";
		if ((string) ($row["field_mode"] ?? "") === "manual") {
			$field_lines = $this->normalize_field_lines((string) ($row["manual_fields_text"] ?? ""));
			if (!empty($field_lines)) {
				$field_block = "- " . implode("\n- ", $field_lines);
			}
		}
		$plan_block = "- " . implode("\n- ", $plan_lines);

		return trim(
"【変更種別】\n" .
(((string) ($row["create_mode"] ?? "") === "child") ? "子ノート追加\n\n" : "ノート追加\n\n") .
"【目的】\n" .
$row["purpose"] . "\n\n" .
"【対象プロジェクト】\n" .
$row["project_name"] . "（現在稼働ディレクトリを自動採用）\n\n" .
"【親ノート】\n" .
(((string) ($row["create_mode"] ?? "") === "child")
	? ((string) ($row["parent_tb_name"] ?? "") . " (" . (string) ($row["parent_menu_name"] ?? "") . ")")
	: "なし") . "\n\n" .
"【作成対象】\n" .
"- ノート名（画面表示名）: " . $row["note_title"] . "\n" .
"- db名: 用途とノート名から適切に決定してください\n" .
"- 項目設定方法: " . $row["field_mode_label"] . "\n" .
"- 項目指定\n" .
$field_block . "\n\n" .
"【Codexに任せる内容】\n" .
"- db名決定\n" .
"- db追加\n" .
"- db_fields追加\n" .
"- screen_fields設定（標準画面で何を表示するかも含む）\n\n" .
(((string) ($row["create_mode"] ?? "") === "child")
	? "【親子ノート要件】\n- 親ノートの db_id=" . (string) ($row["parent_db_id"] ?? "") . " を参照し、parent_tb_id を設定して子ノートとして作成してください\n- 子ノートなので parent_id フィールド連携も確認してください\n\n"
	: "") .
"【実行計画】\n" .
$plan_block . "\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 更新内容が data_get または data_list で確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_parent_child_dropdown_preview_label(array $row): string {
		if ((string) ($row["dropdown_item_display_type"] ?? "") === "template") {
			return (string) ($row["dropdown_item_template"] ?? "");
		}
		return (string) ($row["dropdown_item"] ?? "");
	}

	private function get_table_create_field_type_options(): array {
		return [
			"text" => "Text",
			"number" => "Number(Integer)",
			"float" => "Number(Float)",
			"textarea" => "Textarea",
			"markdown" => "Markdown",
			"dropdown" => "Dropdown",
			"checkbox" => "Checkbox",
			"radio" => "Radio",
			"date" => "Date",
			"datetime" => "Date & Time",
			"year_month" => "Year/Month",
			"file" => "File",
			"image" => "Image"
		];
	}

	private function get_original_form_place_options(bool $is_child): array {
		$opt = [
			"0" => "Top Section",
			"1" => "Each Row"
		];
		if ($is_child) {
			$opt["2"] = "Bottom Section of side table";
			$opt["3"] = "Each Row of side table";
		}
		return $opt;
	}

	private function build_original_form_prompt_text(array $row): string {
		$is_child = ((int) ($row["parent_tb_id"] ?? 0)) > 0;
		$place_opt = $this->get_original_form_place_options($is_child);
		$place = (string) ($row["place"] ?? "");
		$place_label = $place_opt[$place] ?? "Top Section";
		$db_id = $this->normalize_single_id($row["db_id"] ?? "");
		$button_title = trim((string) ($row["button_title"] ?? ""));
		$request_text = trim((string) ($row["request_text"] ?? ""));

		return trim(
"【変更種別】\n" .
"db_additionals / Original Form 追加\n\n" .
"【対象テーブル】\n" .
(string) ($row["tb_name"] ?? "") . " (db_id=" . $db_id . ")\n\n" .
"【ボタン配置場所】\n" .
$place_label . " (place=" . $place . ")\n\n" .
"【ボタン名】\n" .
$button_title . "\n\n" .
"【制作内容】\n" .
$request_text . "\n\n" .
"【実装方針】\n" .
"- db_additionals の Original Form を作成\n" .
"- db_exe を呼ぶ場合は必ず db_id を渡す（tb_nameのみで呼ばない）\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 更新内容が data_get または data_list で確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_pdf_prompt_text(Controller $ctl, array $row): string {
		$is_child = ((int) ($row["parent_tb_id"] ?? 0)) > 0;
		$place_opt = $this->get_original_form_place_options($is_child);
		$place = (string) ($row["place"] ?? "");
		$place_label = $place_opt[$place] ?? "Top Section";
		$db_id = $this->normalize_single_id($row["db_id"] ?? "");
		$button_title = trim((string) ($row["button_title"] ?? ""));
		$request_text = trim((string) ($row["request_text"] ?? ""));
		$all_rows = $this->get_table_field_detail_rows($ctl, (string) ($row["tb_name"] ?? ""));
		$selected_rows = $this->build_pdf_selected_field_rows($all_rows, $row["field_ids"] ?? []);
		$selected_field_lines = [];
		foreach ($selected_rows as $one) {
			$selected_field_lines[] = "field_name=" . (string) ($one["field_name"] ?? "") .
				", title=" . (string) ($one["title"] ?? "") .
				", options=" . (string) ($one["options_text"] ?? "-");
		}
		$selected_field_block = "- （未選択）";
		if (count($selected_field_lines) > 0) {
			$selected_field_block = "- " . implode("\n- ", $selected_field_lines);
		}

		return trim(
"【変更種別】\n" .
"db_additionals / PDF 追加\n\n" .
"【対象テーブル】\n" .
(string) ($row["tb_name"] ?? "") . " (db_id=" . $db_id . ")\n\n" .
"【ボタン配置場所】\n" .
$place_label . " (place=" . $place . ")\n\n" .
"【ボタン名】\n" .
$button_title . "\n\n" .
"【使用フィールド】\n" .
$selected_field_block . "\n\n" .
"【PDF制作内容】\n" .
$request_text . "\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 更新内容が data_get または data_list で確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_csv_download_prompt_text(Controller $ctl, array $row): string {
		$is_child = ((int) ($row["parent_tb_id"] ?? 0)) > 0;
		$place_opt = $this->get_original_form_place_options($is_child);
		$place = (string) ($row["place"] ?? "");
		$place_label = $place_opt[$place] ?? "Top Section";
		$db_id = $this->normalize_single_id($row["db_id"] ?? "");
		$button_title = trim((string) ($row["button_title"] ?? ""));
		$request_text = trim((string) ($row["request_text"] ?? ""));
		$all_rows = $this->get_table_field_detail_rows($ctl, (string) ($row["tb_name"] ?? ""));
		$selected_rows = $this->build_pdf_selected_field_rows($all_rows, $row["field_ids"] ?? []);
		$selected_field_lines = [];
		foreach ($selected_rows as $one) {
			$selected_field_lines[] = "field_name=" . (string) ($one["field_name"] ?? "") .
				", title=" . (string) ($one["title"] ?? "") .
				", options=" . (string) ($one["options_text"] ?? "-");
		}
		$selected_field_block = "- （未選択）";
		if (count($selected_field_lines) > 0) {
			$selected_field_block = "- " . implode("\n- ", $selected_field_lines);
		}

		return trim(
"【変更種別】\n" .
"db_additionals / CSV Download 追加\n\n" .
"【対象テーブル】\n" .
(string) ($row["tb_name"] ?? "") . " (db_id=" . $db_id . ")\n\n" .
"【ボタン配置場所】\n" .
$place_label . " (place=" . $place . ")\n\n" .
"【ボタン名】\n" .
$button_title . "\n\n" .
"【使用フィールド】\n" .
$selected_field_block . "\n\n" .
"【CSV Download 制作内容】\n" .
$request_text . "\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 更新内容が data_get または data_list で確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_csv_upload_prompt_text(Controller $ctl, array $row): string {
		$is_child = ((int) ($row["parent_tb_id"] ?? 0)) > 0;
		$place_opt = $this->get_original_form_place_options($is_child);
		$place = (string) ($row["place"] ?? "");
		$place_label = $place_opt[$place] ?? "Top Section";
		$db_id = $this->normalize_single_id($row["db_id"] ?? "");
		$button_title = trim((string) ($row["button_title"] ?? ""));
		$request_text = trim((string) ($row["request_text"] ?? ""));
		$all_rows = $this->get_table_field_detail_rows($ctl, (string) ($row["tb_name"] ?? ""));
		$selected_rows = $this->build_pdf_selected_field_rows($all_rows, $row["field_ids"] ?? []);
		$selected_field_lines = [];
		foreach ($selected_rows as $one) {
			$selected_field_lines[] = "field_name=" . (string) ($one["field_name"] ?? "") .
				", title=" . (string) ($one["title"] ?? "") .
				", options=" . (string) ($one["options_text"] ?? "-");
		}
		$selected_field_block = "- （未選択）";
		if (count($selected_field_lines) > 0) {
			$selected_field_block = "- " . implode("\n- ", $selected_field_lines);
		}

		return trim(
"【変更種別】\n" .
"db_additionals / CSV Upload 追加\n\n" .
"【対象テーブル】\n" .
(string) ($row["tb_name"] ?? "") . " (db_id=" . $db_id . ")\n\n" .
"【ボタン配置場所】\n" .
$place_label . " (place=" . $place . ")\n\n" .
"【ボタン名】\n" .
$button_title . "\n\n" .
"【使用フィールド】\n" .
$selected_field_block . "\n\n" .
"【CSV Upload 制作内容】\n" .
$request_text . "\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 更新内容が data_get または data_list で確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_chart_prompt_text(Controller $ctl, array $row): string {
		$is_child = ((int) ($row["parent_tb_id"] ?? 0)) > 0;
		$place_opt = $this->get_original_form_place_options($is_child);
		$chart_opt = $this->get_chart_type_options();
		$place = (string) ($row["place"] ?? "");
		$place_label = $place_opt[$place] ?? "Top Section";
		$chart_type = (string) ($row["chart_type"] ?? "");
		$chart_label = $chart_opt[$chart_type] ?? $chart_type;
		$db_id = $this->normalize_single_id($row["db_id"] ?? "");
		$button_title = trim((string) ($row["button_title"] ?? ""));
		$request_text = trim((string) ($row["request_text"] ?? ""));
		$all_rows = $this->get_table_field_detail_rows($ctl, (string) ($row["tb_name"] ?? ""));
		$selected_rows = $this->build_pdf_selected_field_rows($all_rows, $row["field_ids"] ?? []);
		$selected_field_lines = [];
		foreach ($selected_rows as $one) {
			$selected_field_lines[] = "field_name=" . (string) ($one["field_name"] ?? "") .
				", title=" . (string) ($one["title"] ?? "") .
				", options=" . (string) ($one["options_text"] ?? "-");
		}
		$selected_field_block = "- （未選択）";
		if (count($selected_field_lines) > 0) {
			$selected_field_block = "- " . implode("\n- ", $selected_field_lines);
		}

		return trim(
"【変更種別】\n" .
"db_additionals / Chart 追加\n\n" .
"【対象テーブル】\n" .
(string) ($row["tb_name"] ?? "") . " (db_id=" . $db_id . ")\n\n" .
"【ボタン配置場所】\n" .
$place_label . " (place=" . $place . ")\n\n" .
"【チャート種類】\n" .
$chart_label . " (" . $chart_type . ")\n\n" .
"【ボタン名】\n" .
$button_title . "\n\n" .
"【集計に使うフィールド】\n" .
$selected_field_block . "\n\n" .
"【Chart 制作内容】\n" .
$request_text . "\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 更新内容が data_get または data_list で確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_line_message_prompt_text(array $row): string {
		$is_child = ((int) ($row["parent_tb_id"] ?? 0)) > 0;
		$place_opt = $this->get_original_form_place_options($is_child);
		$place = (string) ($row["place"] ?? "");
		$place_label = $place_opt[$place] ?? "Top Section";
		$db_id = $this->normalize_single_id($row["db_id"] ?? "");
		$button_title = trim((string) ($row["button_title"] ?? ""));
		$request_text = trim((string) ($row["request_text"] ?? ""));

		return trim(
"【変更種別】\n" .
"db_additionals / LINEメッセージ送信 追加\n\n" .
"【対象テーブル】\n" .
(string) ($row["tb_name"] ?? "") . " (db_id=" . $db_id . ")\n\n" .
"【ボタン配置場所】\n" .
$place_label . " (place=" . $place . ")\n\n" .
"【ボタン名】\n" .
$button_title . "\n\n" .
"【送信先LINE会員DB】\n" .
"- テーブル: line_member\n" .
"- userid フィールド: userid\n" .
"- 表示名フィールド: line_name\n" .
"- 名前フィールド: name\n\n" .
"【制作内容】\n" .
$request_text . "\n\n" .
"【実装方針】\n" .
"- db_additionals の LINEメッセージ送信機能を作成する\n" .
"- 送信先会員DBは line_member を固定で使用する\n" .
"- LINE送信先の user_id は line_member.userid を使用する\n" .
"- 必要に応じて対象テーブルと line_member の関連付け・絞り込み条件を提案してよい\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 更新内容が data_get または data_list で確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_db_additionals_edit_prompt_text(Controller $ctl, array $row): string {
		$place = (string) ($row["place"] ?? "");
		$additional_id = $this->normalize_single_id($row["additional_id"] ?? "");
		$tb_name = (string) ($row["tb_name"] ?? "");
		$note_name = trim((string) ($row["note_name"] ?? ""));
		$button_title = trim((string) ($row["button_title"] ?? ""));
		$class_name = trim((string) ($row["class_name"] ?? ""));
		$function_name = trim((string) ($row["function_name"] ?? ""));
		$request_text = trim((string) ($row["request_text"] ?? ""));

		return trim(
"【変更種別】\n" .
"db_additionals / 制作済みボタン変更\n\n" .
"【対象ボタンID】\n" .
$additional_id . "\n\n" .
"【対象ノート】\n" .
($note_name !== "" ? $note_name : $tb_name) . " (tb_name=" . $tb_name . ")\n\n" .
"【既存ボタン名】\n" .
$button_title . "\n\n" .
"【既存 class / function】\n" .
$class_name . " / " . $function_name . "\n\n" .
"【既存配置場所】\n" .
$this->get_db_additionals_place_label($place) . " (place=" . $place . ")\n\n" .
"【変更内容】\n" .
$request_text . "\n\n" .
"【実装方針】\n" .
"- 既存の db_additionals ボタンを前提に変更する\n" .
"- 対象 additionals レコードと既存 class/function の実装内容を確認してから改修する\n" .
"- button_title / place / class / function の変更が必要なら整合するように更新する\n" .
"- 既存導線を壊さないように関連テンプレート・PHP・登録値を最小差分で調整する\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 更新内容が data_get または data_list で確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function resolve_line_message_send_mode(string $place): string {
		$place = trim($place);
		if ($place === "1" || $place === "3") {
			return "個別送信";
		}
		return "一括送信";
	}

	private function build_line_message_default_request_text(string $send_mode): string {
		return $send_mode . "。テキストを入力できるフォーム、確認画面があり、line_memberにテキストメッセージを送信する。";
	}

	private function is_line_message_default_request_text(string $text): bool {
		$text = trim($text);
		if ($text === "") {
			return true;
		}
		return in_array($text, [
			$this->build_line_message_default_request_text("一括送信"),
			$this->build_line_message_default_request_text("個別送信")
		], true);
	}

	private function get_chart_type_options(): array {
		return [
			"bar" => "Bar",
			"line" => "Line",
			"pie" => "Pie",
			"doughnut" => "Doughnut",
			"radar" => "Radar",
			"polarArea" => "Polar Area"
		];
	}

	private function get_line_bot_event_options(): array {
		return [
			"follow" => "友達追加時",
			"keyword" => "キーワード入力時",
			"unmatch" => "キーワード未一致時"
		];
	}

	private function build_line_bot_keyword_preview(array $state): string {
		$event_type = (string) ($state["event_type"] ?? "");
		if ($event_type === "follow") {
			return "[follow]";
		}
		if ($event_type === "unmatch") {
			return "[unmatch]";
		}
		return trim((string) ($state["keyword"] ?? ""));
	}

	private function build_line_bot_default_title(string $event_type): string {
		$event_type = trim($event_type);
		if ($event_type === "follow") {
			return "Line Botイベント処理（友達追加時）";
		}
		if ($event_type === "keyword") {
			return "Line Botイベント処理（キーワード入力時）";
		}
		if ($event_type === "unmatch") {
			return "Line Botイベント処理（キーワード未一致時）";
		}
		return "Line Botイベント処理";
	}

	private function find_line_bot_duplicate_keyword(Controller $ctl, string $keyword): ?array {
		$keyword = trim($keyword);
		if ($keyword === "") {
			return null;
		}
		$list = $ctl->db("webhook_rule", "webhook_rule")->select(["channel", "keyword"], ["0", $keyword], true, "AND", "id", SORT_ASC);
		if (!is_array($list) || count($list) === 0) {
			return null;
		}
		return $list[0];
	}

	private function get_table_field_name_options(Controller $ctl, string $tb_name, bool $allow_empty = false): array {
		$rows = $this->get_table_field_detail_rows($ctl, $tb_name);
		$opt = $allow_empty ? ["" => $ctl->t("wizard.select_placeholder")] : [];
		foreach ($rows as $one) {
			$field_name = trim((string) ($one["field_name"] ?? ""));
			$title = trim((string) ($one["title"] ?? ""));
			if ($field_name === "") {
				continue;
			}
			$label = $field_name;
			if ($title !== "" && $title !== $field_name) {
				$label .= " (" . $title . ")";
			}
			$opt[$field_name] = $label;
		}
		return $opt;
	}

	private function guess_line_member_link_fields(Controller $ctl, string $tb_name): array {
		$rows = $this->get_table_field_detail_rows($ctl, $tb_name);
		$field_names = [];
		foreach ($rows as $one) {
			$field_name = trim((string) ($one["field_name"] ?? ""));
			if ($field_name !== "") {
				$field_names[] = $field_name;
			}
		}
		return [
			"user_id_field" => $this->pick_first_existing_field($field_names, ["userid", "user_id", "line_user_id", "line_userid"]),
			"display_name_field" => $this->pick_first_existing_field($field_names, ["line_name", "display_name", "name"]),
			"name_field" => $this->pick_first_existing_field($field_names, ["name", "member_name", "line_name"])
		];
	}

	private function pick_first_existing_field(array $field_names, array $candidates): string {
		$map = [];
		foreach ($field_names as $field_name) {
			$map[strtolower($field_name)] = $field_name;
		}
		foreach ($candidates as $candidate) {
			$key = strtolower($candidate);
			if (isset($map[$key])) {
				return $map[$key];
			}
		}
		return "";
	}

	private function get_db_additionals_target_options(Controller $ctl): array {
		$opt = ["" => $ctl->t("wizard.select_placeholder")];
		$list = $ctl->db("additionals", "db_additionals")->getall("sort", SORT_ASC);
		if (!is_array($list)) {
			return $opt;
		}
		foreach ($list as $one) {
			$id = $this->normalize_single_id($one["id"] ?? "");
			$tb_name = trim((string) ($one["tb_name"] ?? ""));
			$button_title = trim((string) ($one["button_title"] ?? ""));
			$class_name = trim((string) ($one["class_name"] ?? ""));
			if ($id === "" || $tb_name === "" || $button_title === "" || $class_name === "admin") {
				continue;
			}
			$note_name = $this->resolve_db_additionals_note_name($ctl, $tb_name);
			$opt[$id] = ($note_name !== "" ? $note_name : $tb_name) . " " . $button_title;
		}
		return $opt;
	}

	private function get_db_additionals_target(Controller $ctl, string $additional_id): ?array {
		$additional_id = $this->normalize_single_id($additional_id);
		if ($additional_id === "") {
			return null;
		}
		$item = $ctl->db("additionals", "db_additionals")->get((int) $additional_id);
		if (!is_array($item) || count($item) === 0) {
			return null;
		}
		$tb_name = trim((string) ($item["tb_name"] ?? ""));
		$button_title = trim((string) ($item["button_title"] ?? ""));
		$class_name = trim((string) ($item["class_name"] ?? ""));
		if ($tb_name === "" || $button_title === "" || $class_name === "admin") {
			return null;
		}
		return $item;
	}

	private function build_db_additionals_target_state(Controller $ctl, array $item): array {
		$tb_name = trim((string) ($item["tb_name"] ?? ""));
		return [
			"additional_id" => $this->normalize_single_id($item["id"] ?? ""),
			"tb_name" => $tb_name,
			"note_name" => $this->resolve_db_additionals_note_name($ctl, $tb_name),
			"button_title" => trim((string) ($item["button_title"] ?? "")),
			"class_name" => trim((string) ($item["class_name"] ?? "")),
			"function_name" => trim((string) ($item["function_name"] ?? "")),
			"place" => trim((string) ($item["place"] ?? ""))
		];
	}

	private function resolve_db_additionals_note_name(Controller $ctl, string $tb_name): string {
		$db = $this->find_db_row_by_tb_name($ctl, $tb_name);
		if (!is_array($db) || count($db) === 0) {
			return $tb_name;
		}
		$menu_name = trim((string) ($db["menu_name"] ?? ""));
		return $menu_name !== "" ? $menu_name : $tb_name;
	}

	private function get_db_additionals_place_label(string $place): string {
		$is_child = $place === "2" || $place === "3";
		$place_opt = $this->get_original_form_place_options($is_child);
		return $place_opt[$place] ?? $place;
	}

	private function get_db_additionals_sort_list(Controller $ctl, string $tb_name, string $place): array {
		$tb_name = $this->normalize_table_name($tb_name);
		$place = trim($place);
		if ($tb_name === "" || $place === "") {
			return [];
		}
		$list = $ctl->db("additionals", "db_additionals")->select(["tb_name", "place"], [$tb_name, $place], true, "AND", "sort", SORT_ASC);
		return is_array($list) ? $list : [];
	}

	private function get_line_bot_edit_rule_options(Controller $ctl): array {
		$opt = ["" => $ctl->t("wizard.select_placeholder")];
		$list = $ctl->db("webhook_rule", "webhook_rule")->select("channel", "0", true, "AND", "sort", SORT_ASC);
		if (!is_array($list)) {
			return $opt;
		}
		foreach ($list as $one) {
			$id = $this->normalize_single_id($one["id"] ?? "");
			if ($id === "") {
				continue;
			}
			$event_type = $this->detect_line_bot_rule_event_type($one);
			if ($event_type === "") {
				continue;
			}
			$keyword = trim((string) ($one["keyword"] ?? ""));
			$action_class = trim((string) ($one["action_class"] ?? ""));
			if ($event_type === "follow") {
				$label = "[follow] (友達追加時)";
			} else if ($event_type === "unmatch") {
				$label = "[unmatch] (キーワード未一致時)";
			} else {
				$label = $keyword . " (キーワード入力時)";
			}
			if ($action_class !== "") {
				$label .= " / " . $action_class;
			}
			$opt[$id] = $label;
		}
		return $opt;
	}

	private function get_line_bot_editable_rule(Controller $ctl, string $rule_id): ?array {
		$rule_id = $this->normalize_single_id($rule_id);
		if ($rule_id === "") {
			return null;
		}
		$data = $ctl->db("webhook_rule", "webhook_rule")->get((int) $rule_id);
		if (!is_array($data) || count($data) === 0) {
			return null;
		}
		if (trim((string) ($data["channel"] ?? "")) !== "0") {
			return null;
		}
		return $this->detect_line_bot_rule_event_type($data) === "" ? null : $data;
	}

	private function detect_line_bot_rule_event_type(array $rule): string {
		$keyword = trim((string) ($rule["keyword"] ?? ""));
		$match_type = trim((string) ($rule["match_type"] ?? ""));
		if ($match_type === "data_type" && $keyword === "[follow]") {
			return "follow";
		}
		if ($match_type === "unmatch" && $keyword === "[unmatch]") {
			return "unmatch";
		}
		if ($match_type === "exact" && $keyword !== "") {
			return "keyword";
		}
		return "";
	}

	private function find_line_bot_duplicate_keyword_except(Controller $ctl, string $keyword, string $exclude_id): ?array {
		$keyword = trim($keyword);
		$exclude_id = $this->normalize_single_id($exclude_id);
		if ($keyword === "") {
			return null;
		}
		$list = $ctl->db("webhook_rule", "webhook_rule")->select(["channel", "keyword"], ["0", $keyword], true, "AND", "id", SORT_ASC);
		if (!is_array($list) || count($list) === 0) {
			return null;
		}
		foreach ($list as $one) {
			$id = $this->normalize_single_id($one["id"] ?? "");
			if ($exclude_id !== "" && $id === $exclude_id) {
				continue;
			}
			return $one;
		}
		return null;
	}

	private function build_line_bot_add_prompt_text(array $row): string {
		$event_type = (string) ($row["event_type"] ?? "");
		$event_opt = $this->get_line_bot_event_options();
		$event_label = $event_opt[$event_type] ?? $event_type;
		$button_title = trim((string) ($row["button_title"] ?? ""));
		$request_text = trim((string) ($row["request_text"] ?? ""));
		$keyword = $this->build_line_bot_keyword_preview($row);
		$match_type = $event_type === "follow" ? "data_type" : ($event_type === "unmatch" ? "unmatch" : "exact");

		return trim(
"【変更種別】\n" .
"Line Bot処理追加\n\n" .
"【イベント】\n" .
$event_label . " (" . $event_type . ")\n\n" .
"【keyword】\n" .
$keyword . "\n\n" .
"【match_type】\n" .
$match_type . "\n\n" .
"【処理名】\n" .
$button_title . "\n\n" .
"【制作内容】\n" .
$request_text . "\n\n" .
"【実装方針】\n" .
"- webhook_rule と webhook_line の流れに沿って実装する\n" .
"- channel は LINE(0) を使用する\n" .
"- 友達追加時は [follow] / data_type として扱う\n" .
"- キーワード入力時は exact として扱う\n" .
"- キーワード未一致時は [unmatch] / unmatch として扱う\n" .
"\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- webhook_rule の重複がない\n" .
"- app_call 成功\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_line_bot_edit_prompt_text(array $row): string {
		$event_type = (string) ($row["event_type"] ?? "");
		$event_opt = $this->get_line_bot_event_options();
		$event_label = $event_opt[$event_type] ?? $event_type;
		$button_title = trim((string) ($row["button_title"] ?? ""));
		$request_text = trim((string) ($row["request_text"] ?? ""));
		$keyword = $this->build_line_bot_keyword_preview($row);
		$match_type = $event_type === "follow" ? "data_type" : ($event_type === "unmatch" ? "unmatch" : "exact");
		$action_class = trim((string) ($row["action_class"] ?? ""));
		$rule_id = $this->normalize_single_id($row["rule_id"] ?? "");

		return trim(
"【変更種別】\n" .
"Line Bot処理変更\n\n" .
"【対象ルールID】\n" .
$rule_id . "\n\n" .
"【既存 action_class】\n" .
($action_class !== "" ? $action_class : "(不明)") . "\n\n" .
"【変更後イベント】\n" .
$event_label . " (" . $event_type . ")\n\n" .
"【変更後 keyword】\n" .
$keyword . "\n\n" .
"【変更後 match_type】\n" .
$match_type . "\n\n" .
"【処理名】\n" .
$button_title . "\n\n" .
"【制作内容】\n" .
$request_text . "\n\n" .
"【実装方針】\n" .
"- 既存 webhook_rule を尊重し、対象ルールを変更する\n" .
"- channel は LINE(0) を使用する\n" .
"- 友達追加時は [follow] / data_type として扱う\n" .
"- キーワード入力時は exact として扱う\n" .
"- キーワード未一致時は [unmatch] / unmatch として扱う\n" .
"- 既存 action_class を起点に必要な変更を行う\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- webhook_rule の重複がない\n" .
"- app_call 成功\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_line_bot_delete_prompt_text(array $row): string {
		$event_type = (string) ($row["event_type"] ?? "");
		$event_opt = $this->get_line_bot_event_options();
		$event_label = $event_opt[$event_type] ?? $event_type;
		$rule_id = $this->normalize_single_id($row["rule_id"] ?? "");
		$keyword = trim((string) ($row["keyword"] ?? ""));
		$action_class = trim((string) ($row["action_class"] ?? ""));

		return trim(
"【変更種別】\n" .
"Line Bot処理削除\n\n" .
"【対象ルールID】\n" .
$rule_id . "\n\n" .
"【イベント】\n" .
$event_label . " (" . $event_type . ")\n\n" .
"【keyword】\n" .
$keyword . "\n\n" .
"【action_class】\n" .
$action_class . "\n\n" .
"【実装方針】\n" .
"- 対象 webhook_rule を削除する\n" .
"- 関連する action_class / テンプレート / 登録導線も確認し、不要なら削除する\n" .
"- channel は LINE(0) を対象とする\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- 対象 webhook_rule が削除されている\n" .
"- app_call 成功\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function delete_line_bot_action_class_dir(string $action_class): void {
		$action_class = trim($action_class);
		if ($action_class === "" || !preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $action_class)) {
			return;
		}
		$dirs = new Dirs();
		$base_dir = realpath($dirs->appdir_user);
		if ($base_dir === false) {
			return;
		}
		$target_dir = $dirs->appdir_user . "/" . $action_class;
		if (!is_dir($target_dir)) {
			return;
		}
		$target_real = realpath($target_dir);
		if ($target_real === false) {
			return;
		}
		if (strpos($target_real, $base_dir . DIRECTORY_SEPARATOR) !== 0) {
			return;
		}
		$this->delete_directory_recursive($target_real);
	}

	private function delete_directory_recursive(string $dir): void {
		if (!is_dir($dir)) {
			return;
		}
		$items = scandir($dir);
		if (!is_array($items)) {
			return;
		}
		foreach ($items as $item) {
			if ($item === "." || $item === "..") {
				continue;
			}
			$path = $dir . DIRECTORY_SEPARATOR . $item;
			if (is_dir($path) && !is_link($path)) {
				$this->delete_directory_recursive($path);
				continue;
			}
			if (file_exists($path) || is_link($path)) {
				@unlink($path);
			}
		}
		@rmdir($dir);
	}

	private function build_line_member_link_prompt_text(Controller $ctl, array $row): string {
		$user_id_field = (string) ($row["user_id_field"] ?? "");
		$display_name_field = (string) ($row["display_name_field"] ?? "");
		$name_field = (string) ($row["name_field"] ?? "");
		$button_title = trim((string) ($row["button_title"] ?? ""));
		$request_text = trim((string) ($row["request_text"] ?? ""));

		return trim(
"【変更種別】\n" .
"LINE用会員データベース作製\n\n" .
"【対象テーブル】\n" .
(string) ($row["tb_name"] ?? "") . "\n\n" .
"【必須項目】\n" .
"- " . $user_id_field . ": LINE user_id\n" .
"- " . $display_name_field . ": 表示名\n" .
"- " . ($name_field !== "" ? $name_field : "name") . ": 名前\n" .
"- 会員種別: member_type (dropdown / member_type_opt)\n" .
"【要件】\n" .
"- 会員DBテーブルを新規作成する\n" .
"- constant_array に member_type_opt を追加し、会員=0 / 管理者=1 を定義する\n" .
"- webhook_line 標準の getting_member 解決処理を前提にする\n" .
"- getting_member の webhook_rule / action_class は作成しない\n" .
"- fbp/ 以下は変更しない\n" .
"- webhook_line 標準 getting_member は既に実装済みとして扱う\n" .
"- フレームワーク側の追加修正は不要\n" .
"- 会員テーブルの必要項目は userid / line_name / name / member_type を必須とする\n" .
"- 未登録時は line_name / userid / name / member_type=0 で新規作成される前提を崩さない\n" .
"- 管理者通知や manager 検索で webhook_line が member_type=1 を参照している前提を崩さない\n" .
"- LINE会員テーブル設定では複製アイコンを Hide をデフォルトにする\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_cron_add_prompt_text(array $row): string {
		$timing_text = trim((string) ($row["timing_text"] ?? ""));
		$request_text = trim((string) ($row["request_text"] ?? ""));

		return trim(
"【変更種別】\n" .
"Cron / 定期処理追加\n\n" .
"【実行タイミング】\n" .
$timing_text . "\n\n" .
"【実行内容】\n" .
$request_text . "\n\n" .
"【実装方針】\n" .
"- cron の定期処理を追加する\n" .
"- 実行タイミングと実行内容から、title / class_name / function_name / min / hour / day / month / weekday を Codex 側で決定する\n" .
"- cron テーブルの min/hour/day/month/weekday 形式で登録する\n" .
"- class_name と function_name は PHP で扱いやすい分かりやすい名前にする\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 更新内容が data_get または data_list で確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_cron_edit_prompt_text(array $row): string {
		$min = $this->normalize_cron_component_list($row["min"] ?? null);
		$hour = $this->normalize_cron_component_list($row["hour"] ?? null);
		$day = $this->normalize_cron_component_list($row["day"] ?? null);
		$month = $this->normalize_cron_component_list($row["month"] ?? null);
		$weekday = $this->normalize_cron_component_list($row["weekday"] ?? null);
		$cron_id = $this->normalize_single_id($row["cron_id"] ?? "");
		$title = trim((string) ($row["title"] ?? ""));
		$class_name = trim((string) ($row["class_name"] ?? ""));
		$function_name = trim((string) ($row["function_name"] ?? ""));
		$timing_text = trim((string) ($row["timing_text"] ?? ""));
		$summary = trim((string) ($row["summary"] ?? ""));
		$request_text = trim((string) ($row["request_text"] ?? ""));

		return trim(
"【変更種別】\n" .
"Cron / 定期処理変更\n\n" .
"【対象Cron ID】\n" .
$cron_id . "\n\n" .
"【現在の定期処理名】\n" .
$title . "\n\n" .
"【現在のclass_name】\n" .
$class_name . "\n\n" .
"【現在のfunction_name】\n" .
$function_name . "\n\n" .
"【現在のcron登録値】\n" .
"- min: " . $this->build_cron_component_text($min) . "\n" .
"- hour: " . $this->build_cron_component_text($hour) . "\n" .
"- day: " . $this->build_cron_component_text($day) . "\n" .
"- month: " . $this->build_cron_component_text($month) . "\n" .
"- weekday: " . $this->build_cron_component_text($weekday) . "\n\n" .
"【変更後の実行タイミング】\n" .
$timing_text . "\n\n" .
"【変更内容】\n" .
$request_text . "\n\n" .
"【実装方針】\n" .
"- 既存 cron レコードを変更する\n" .
"- cron テーブルの min/hour/day/month/weekday 形式で更新する\n" .
"- 現在の登録値と変更後の実行タイミング・変更内容から、必要な title / class_name / function_name / min / hour / day / month / weekday の変更を Codex 側で決定する\n" .
"- 必要に応じて既存 class/function の調整も行ってよい\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 更新内容が data_get または data_list で確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_public_pages_common_design_prompt_text(array $row): string {
		$request_text = $this->build_public_pages_common_design_request_text($row);
		$public_asset_text = trim((string) ($row["public_asset_text"] ?? ""));
		return trim(
"【変更種別】\n" .
"public_pages / 共通デザイン（ヘッダ・フッタ）作製\n\n" .
"【対象クラス】\n" .
"public_pages\n\n" .
"【対象テンプレート / ファイル】\n" .
"- classes/app/public_pages/public_pages.php のうち show_public_pages() を呼ぶ箇所\n" .
"- show_public_pages() の第3引数で使う共通テンプレート\n" .
"- show_public_pages() の第4引数で使う共通テンプレート\n" .
"- classes/app/public_pages/style.css\n" .
"- 必要な場合のみ fbp/Templates/publicsite_index.tpl\n\n" .
"【設計前提】\n" .
"- 全公開ページのラップは publicsite_index.tpl を前提にする\n" .
"- publicsite_index.tpl は公開ページ共通の骨組みに限定し、ブランド固有文言を固定で持たせない\n" .
"- 共通デザインの可視部分は show_public_pages() の第3引数 / 第4引数テンプレート側で管理する\n" .
"- 共通CSSは classes/app/public_pages/style.css 側で管理する\n" .
"- ページ固有の本文テンプレートには共通ヘッダ・フッタを重複実装しない\n" .
"- show_public_pages() 前提の構造を崩さない\n\n" .
"【実装配置ルール】\n" .
	"- publicsite_index.tpl 側には html_header / contents_header / contents / contents_footer の配置枠だけを置く\n" .
	"- 共通デザインの主対象は show_public_pages() の第3引数 / 第4引数テンプレートにする\n" .
	"- 共通CSS追加は classes/app/public_pages/style.css 側に置く\n" .
	"- publicsite_header.tpl / publicsite_footer.tpl は head資産や既存script責務が必要な場合だけ触る\n" .
	"- show_public_pages() を呼ぶ public_pages 関数は、必要に応じて共通テンプレートを第3引数 / 第4引数へそろえる\n" .
	"- 画像をテンプレートに出す場合は Smartyヘルパー public_asset_img を使う\n" .
	"- 共通メニューは public_pages_registry から取得して描画する\n" .
	"- メニュー対象は enabled=1 かつ show_in_menu=1 を前提にする\n" .
	"- メニュー表示名は menu_label を優先し、未設定時は title を使う\n" .
	"- メニュー順は menu_sort 昇順を前提にする\n\n" .
"【使用するPublic Assets】\n" .
($public_asset_text !== "" ? $public_asset_text : "- 未選択") . "\n\n" .
"【制作内容】\n" .
$request_text . "\n\n" .
"【実装方針】\n" .
	"- public_pages の共通デザインを作製または調整する\n" .
	"- まず既存の publicsite_index.tpl と show_public_pages() 第3引数 / 第4引数の共通テンプレート、および style.css の責務を確認し、その責務に沿って実装する\n" .
	"- show_public_pages() の共通レイアウトに沿って実装する\n" .
	"- ヘッダ・フッタ・共通ナビゲーションの整合を保つ\n" .
	"- style.css の調整が必要な場合は、デザイン方針の指示に含めて反映する\n" .
	"- Public Assets の画像は public_asset_img を使って出力する\n" .
	"- 共通ナビゲーションは public_pages_registry の menu 設定と整合するように実装する\n" .
	"- 既存の公開ページと競合しないようにする\n\n" .
"【共通導線の扱い】\n" .
"- この段階ではリンク先が未確定でもよい\n" .
"- 後から差し替えやすい実装にする\n" .
"- 必要なら仮リンク、TODOコメント、または差し替えやすいプレースホルダ構造で実装してよい\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_public_pages_common_design_request_text(array $row): string {
		$parts = [];
		$header = trim((string) ($row["common_header_text"] ?? ""));
		$footer = trim((string) ($row["common_footer_text"] ?? ""));
		$nav = trim((string) ($row["common_nav_text"] ?? ""));
		$style = trim((string) ($row["common_style_text"] ?? ""));
		$parts[] = "【本文前共通ブロック要件】\n" . ($header !== "" ? $header : "変更なし");
		$parts[] = "【本文後共通ブロック要件】\n" . ($footer !== "" ? $footer : "変更なし");
		$parts[] = "【ナビ・メニュー】\n" . ($nav !== "" ? $nav : "変更なし");
		$parts[] = "【デザイン方針】\n" . ($style !== "" ? $style : "変更なし");
		$text = implode("\n\n", $parts);
		if ($text === "") {
			return trim((string) ($row["request_text"] ?? ""));
		}
		return $text;
	}

	private function validate_public_pages_common_step(Controller $ctl, string $label): string {
		$value = trim((string) $ctl->POST("step_value"));
		if ($value === "") {
			$ctl->res_error_message("step_value", $ctl->t("wizard.validation.step_value_required", ["label" => $label]));
			return "";
		}
		return $value;
	}

	private function build_public_pages_add_prompt_text(array $row): string {
		$title = trim((string) ($row["title"] ?? ""));
		$function_name = trim((string) ($row["function_name"] ?? ""));
		$template_name = trim((string) ($row["template_name"] ?? ""));
		$public_asset_text = trim((string) ($row["public_asset_text"] ?? ""));
		$request_text = trim((string) ($row["request_text"] ?? ""));
		return trim(
"【変更種別】\n" .
"public_pages / 新規ページ追加\n\n" .
"【ページタイトル】\n" .
$title . "\n\n" .
"【function_name】\n" .
$function_name . "\n\n" .
"【template_name】\n" .
$template_name . "\n\n" .
"【使用するPublic Assets】\n" .
($public_asset_text !== "" ? $public_asset_text : "- 未選択") . "\n\n" .
"【制作内容】\n" .
$request_text . "\n\n" .
"【実装方針】\n" .
"- public_pages クラスに " . $function_name . " を追加する\n" .
"- 必要なテンプレートを追加する\n" .
"- 公開URLは public_pages*" . $function_name . " を前提に設計する\n" .
"- public_pages_registry に title/function_name/template_name/show_in_menu/menu_label/menu_sort を登録または同期する\n" .
"- show_public_pages() を優先して公開導線を構成する\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 主要導線を app_check で確認\n" .
"- 更新内容が data_get または data_list で確認できる\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_public_pages_edit_prompt_text(array $row): string {
		$registry_id = $this->normalize_single_id($row["registry_id"] ?? "");
		$title = trim((string) ($row["title"] ?? ""));
		$function_name = trim((string) ($row["function_name"] ?? ""));
		$template_name = trim((string) ($row["template_name"] ?? ""));
		$public_asset_text = trim((string) ($row["public_asset_text"] ?? ""));
		$request_text = trim((string) ($row["request_text"] ?? ""));
		return trim(
"【変更種別】\n" .
"public_pages / 既存ページ変更\n\n" .
"【対象registry ID】\n" .
$registry_id . "\n\n" .
"【ページタイトル】\n" .
$title . "\n\n" .
"【function_name】\n" .
$function_name . "\n\n" .
"【template_name】\n" .
$template_name . "\n\n" .
"【使用するPublic Assets】\n" .
($public_asset_text !== "" ? $public_asset_text : "- 未選択") . "\n\n" .
"【変更内容】\n" .
$request_text . "\n\n" .
"【実装方針】\n" .
"- public_pages の既存 function を変更する\n" .
"- 必要なテンプレートや関連導線も調整する\n" .
"- public_pages_registry の title/function_name/template_name/show_in_menu/menu_label/menu_sort と整合するよう更新する\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 主要導線を app_check で確認\n" .
"- 更新内容が data_get または data_list で確認できる\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_public_pages_delete_prompt_text(array $row): string {
		$registry_id = $this->normalize_single_id($row["registry_id"] ?? "");
		$title = trim((string) ($row["title"] ?? ""));
		$function_name = trim((string) ($row["function_name"] ?? ""));
		$template_name = trim((string) ($row["template_name"] ?? ""));
		$public_asset_text = trim((string) ($row["public_asset_text"] ?? ""));
		return trim(
"【変更種別】\n" .
"public_pages / 既存ページ削除\n\n" .
"【対象registry ID】\n" .
$registry_id . "\n\n" .
"【ページタイトル】\n" .
$title . "\n\n" .
"【function_name】\n" .
$function_name . "\n\n" .
"【template_name】\n" .
$template_name . "\n\n" .
"【関連Public Assets】\n" .
($public_asset_text !== "" ? $public_asset_text : "- 未選択") . "\n\n" .
"【実装方針】\n" .
"- public_pages の対象 function を削除する\n" .
"- 不要なテンプレートや関連コードも整理する\n" .
"- public_pages_registry の対象レコードも削除する\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 主要導線を app_check で確認\n" .
"- 更新内容が data_get または data_list で確認できる\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_embed_app_prompt_text(array $row): string {
		$action = trim((string) ($row["embed_action"] ?? "add"));
		if ($action === "edit") {
			return $this->build_embed_app_edit_prompt_text($row);
		}
		$title = trim((string) ($row["title"] ?? ""));
		$class_name = trim((string) ($row["class_name"] ?? ""));
		$embed_key = trim((string) ($row["embed_key"] ?? ""));
		if ($embed_key === "") {
			$embed_key = $class_name;
		}
		$request_text = trim((string) ($row["request_text"] ?? ""));

		return trim(
"【変更種別】\n" .
"埋め込みアプリ / 追加\n\n" .
"【タイトル】\n" .
$title . "\n\n" .
"【制作内容】\n" .
$request_text . "\n\n" .
"【実装方針】\n" .
"- router方式の埋め込みアプリを新規作成する\n" .
"- class_name と embed_key は要件に合う分かりやすい名前を Codex 側で決定する\n" .
"- entry point は決定した class_name の page を使う\n" .
"- embed_app の登録値は実装した class_name / embed_key と整合させる\n" .
"- snippet は配信元URL固定で生成する\n" .
"- embed_app_runtime の route / loader_js 導線に接続する\n" .
"- 埋め込みタグ生成と origin 条件の確認まで行う\n" .
"- CLI確認では embed_app_list を使う\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- embed_app_list で登録内容を確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_embed_app_edit_prompt_text(array $row): string {
		$embed_app_id = $this->normalize_single_id($row["embed_app_id"] ?? "");
		$title = trim((string) ($row["title"] ?? ""));
		$class_name = trim((string) ($row["class_name"] ?? ""));
		$embed_key = trim((string) ($row["embed_key"] ?? ""));
		$request_text = trim((string) ($row["request_text"] ?? ""));

		return trim(
"【変更種別】\n" .
"埋め込みアプリ / 既存変更\n\n" .
"【対象ID】\n" .
$embed_app_id . "\n\n" .
"【タイトル】\n" .
$title . "\n\n" .
"【class_name】\n" .
$class_name . "\n\n" .
"【embed_key】\n" .
$embed_key . "\n\n" .
"【変更内容】\n" .
$request_text . "\n\n" .
"【実装方針】\n" .
"- 既存の router方式埋め込みアプリを変更する\n" .
"- entry point は " . $class_name . "::page を前提にする\n" .
"- embed_app の登録値と実装の整合を保つ\n" .
"- snippet は配信元URL固定で生成する\n" .
"- embed_app_runtime の route / loader_js 導線に接続する\n" .
"- CLI確認では embed_app_list を使う\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- embed_app_list で更新内容を確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_embed_app_delete_prompt_text(array $row): string {
		$embed_app_id = $this->normalize_single_id($row["embed_app_id"] ?? "");
		$title = trim((string) ($row["title"] ?? ""));
		$class_name = trim((string) ($row["class_name"] ?? ""));
		$embed_key = trim((string) ($row["embed_key"] ?? ""));

		return trim(
"【変更種別】\n" .
"埋め込みアプリ / 削除\n\n" .
"【対象ID】\n" .
$embed_app_id . "\n\n" .
"【タイトル】\n" .
$title . "\n\n" .
"【class_name】\n" .
$class_name . "\n\n" .
"【embed_key】\n" .
$embed_key . "\n\n" .
"【実装方針】\n" .
"- 対象の embed_app レコードを削除する\n" .
"- 関連するクラス、テンプレート、公開導線への影響を確認する\n" .
"- 不要な関連コードがあれば整理する\n" .
"- CLI確認では embed_app_list を使う\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- embed_app_list で対象が削除済みであることを確認できる\n" .
"- app_call 成功\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_dashboard_prompt_text(array $row): string {
		$action = trim((string) ($row["dashboard_action"] ?? "add"));
		if ($action === "edit") {
			return $this->build_dashboard_edit_prompt_text($row);
		}
		$title = trim((string) ($row["title"] ?? ""));
		$class_name = trim((string) ($row["class_name"] ?? ""));
		$function_name = trim((string) ($row["function_name"] ?? ""));
		$column_width = (int) ($row["column_width"] ?? 1);
		$request_text = trim((string) ($row["request_text"] ?? ""));

		return trim(
"【変更種別】\n" .
"Dashboard / 追加\n\n" .
"【Dashboard 名】\n" .
$title . "\n\n" .
"【column_width】\n" .
$column_width . "\n\n" .
"【制作内容】\n" .
$request_text . "\n\n" .
"【実装方針】\n" .
"- Dashboard 用のウィジェットクラスまたは既存クラスのウィジェット関数を追加する\n" .
"- 新規 class_name は要件に合う分かりやすい名前を Codex 側で決定する\n" .
"- function_name は原則 dashboard を使うが、要件に応じて変更してよい\n" .
"- 表示関数内では show_dashboard_widget() を使う\n" .
"- dashboard テーブルに class_name / function_name / column_width / sort を登録する\n" .
"- column_width は 1,2,3 のいずれかで実装する\n" .
"- dashboard/page はウィジェット登録しない\n" .
"- CLI確認では data_list(table=dashboard,max=100) と app_call(class=dashboard,function=page) を使う\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call(class=dashboard,function=page) 成功\n" .
"- data_list で dashboard 登録内容を確認できる\n" .
"- 主要表示が確認できる\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_dashboard_edit_prompt_text(array $row): string {
		$dashboard_id = $this->normalize_single_id($row["dashboard_id"] ?? "");
		$class_name = trim((string) ($row["class_name"] ?? ""));
		$function_name = trim((string) ($row["function_name"] ?? ""));
		$column_width = (int) ($row["column_width"] ?? 1);
		$request_text = trim((string) ($row["request_text"] ?? ""));

		return trim(
"【変更種別】\n" .
"Dashboard / 変更\n\n" .
"【対象ID】\n" .
$dashboard_id . "\n\n" .
"【class_name】\n" .
$class_name . "\n\n" .
"【function_name】\n" .
$function_name . "\n\n" .
"【column_width】\n" .
$column_width . "\n\n" .
"【変更内容】\n" .
$request_text . "\n\n" .
"【実装方針】\n" .
"- 既存の Dashboard ウィジェットを変更する\n" .
"- 必要なら関連クラス、テンプレート、dashboard テーブルの登録値を更新する\n" .
"- 表示関数内では show_dashboard_widget() を維持する\n" .
"- dashboard/page はウィジェット登録しない\n" .
"- CLI確認では data_list(table=dashboard,max=100) と app_call(class=dashboard,function=page) を使う\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call(class=dashboard,function=page) 成功\n" .
"- data_list で dashboard 更新内容を確認できる\n" .
"- 主要表示が確認できる\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_dashboard_delete_prompt_text(array $row): string {
		$dashboard_id = $this->normalize_single_id($row["dashboard_id"] ?? "");
		$class_name = trim((string) ($row["class_name"] ?? ""));
		$function_name = trim((string) ($row["function_name"] ?? ""));
		$column_width = (int) ($row["column_width"] ?? 1);

		return trim(
"【変更種別】\n" .
"Dashboard / 削除\n\n" .
"【対象ID】\n" .
$dashboard_id . "\n\n" .
"【class_name】\n" .
$class_name . "\n\n" .
"【function_name】\n" .
$function_name . "\n\n" .
"【column_width】\n" .
$column_width . "\n\n" .
"【実装方針】\n" .
"- 対象の dashboard レコードを削除する\n" .
"- 関連するクラスやテンプレートが不要なら整理する\n" .
"- 他の Dashboard 表示に影響がないことを確認する\n" .
"- CLI確認では data_list(table=dashboard,max=100) と app_call(class=dashboard,function=page) を使う\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- data_list で対象が削除済みであることを確認できる\n" .
"- app_call(class=dashboard,function=page) 成功\n" .
"- 主要表示が確認できる\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function get_table_change_state(Controller $ctl): array {
		$state = $ctl->get_session("wizard_table_change_state");
		if (!is_array($state)) {
			$state = [];
		}
		$action = (string) ($state["change_action"] ?? "");
		$fields_text = (string) ($state["fields_text"] ?? "");
		$field_lines = $this->normalize_field_lines($fields_text);
		if ($action === "add_screen_field") {
			$display_matrix = $this->normalize_screen_display_matrix($state["display_matrix"] ?? null, $field_lines);
			$display_targets_text = $this->build_screen_display_matrix_text($fields_text, $display_matrix);
		} else {
			$display_matrix = $this->normalize_display_matrix($state["display_matrix"] ?? null, $field_lines, true);
			$display_targets_text = $this->build_display_matrix_text($fields_text, $display_matrix);
		}
		return [
			"change_action" => $action,
			"target_tb_name" => (string) ($state["target_tb_name"] ?? ""),
			"fields_text" => $this->normalize_fields_text_for_human($fields_text),
			"display_matrix" => $display_matrix,
			"display_targets_text" => $display_targets_text,
			"delete_field_ids" => $this->normalize_id_list($state["delete_field_ids"] ?? null),
			"update_field_ids" => $this->normalize_id_list($state["update_field_ids"] ?? null),
			"update_field_change_text" => (string) ($state["update_field_change_text"] ?? ""),
			"screen_add_field_ids" => $this->normalize_id_list($state["screen_add_field_ids"] ?? null)
		];
	}

	private function save_table_change_state(Controller $ctl, array $state) {
		$action = (string) ($state["change_action"] ?? "");
		$field_lines = $this->normalize_field_lines((string) ($state["fields_text"] ?? ""));
		if ($action === "add_screen_field") {
			$state["display_matrix"] = $this->normalize_screen_display_matrix($state["display_matrix"] ?? null, $field_lines);
		} else {
			$state["display_matrix"] = $this->normalize_display_matrix($state["display_matrix"] ?? null, $field_lines, true);
		}
		$state["delete_field_ids"] = $this->normalize_id_list($state["delete_field_ids"] ?? null);
		$state["update_field_ids"] = $this->normalize_id_list($state["update_field_ids"] ?? null);
		$state["update_field_change_text"] = (string) ($state["update_field_change_text"] ?? "");
		$state["screen_add_field_ids"] = $this->normalize_id_list($state["screen_add_field_ids"] ?? null);
		$ctl->set_session("wizard_table_change_state", $state);
	}

	private function get_table_options(Controller $ctl): array {
		$list = $ctl->db("db", "db")->getall("sort", SORT_ASC);
		$opt = ["" => $ctl->t("wizard.select_placeholder")];
		if (!is_array($list)) {
			return $opt;
		}
		foreach ($list as $one) {
			$tb = trim((string) ($one["tb_name"] ?? ""));
			$menu = trim((string) ($one["menu_name"] ?? ""));
			if ($tb === "") {
				continue;
			}
			$label = $tb;
			if ($menu !== "") {
				$label .= " (" . $menu . ")";
			}
			$opt[$tb] = $label;
		}
		return $opt;
	}

	private function get_parent_table_options(Controller $ctl): array {
		$list = $ctl->db("db", "db")->getall("sort", SORT_ASC);
		$opt = ["" => $ctl->t("wizard.select_placeholder")];
		if (!is_array($list)) {
			return $opt;
		}
		foreach ($list as $one) {
			$tb = trim((string) ($one["tb_name"] ?? ""));
			$id = $this->normalize_single_id($one["id"] ?? "");
			if ($tb === "" || $id === "") {
				continue;
			}
			$menu = trim((string) ($one["menu_name"] ?? ""));
			$label = $tb;
			if ($menu !== "") {
				$label .= " (" . $menu . ")";
			}
			$opt[$tb] = $label;
		}
		return $opt;
	}

	private function get_child_table_options(Controller $ctl): array {
		$list = $ctl->db("db", "db")->getall("sort", SORT_ASC);
		$opt = ["" => $ctl->t("wizard.select_placeholder")];
		if (!is_array($list)) {
			return $opt;
		}
		foreach ($list as $one) {
			$parent_tb_id = (int) ($one["parent_tb_id"] ?? 0);
			if ($parent_tb_id <= 0) {
				continue;
			}
			$tb = trim((string) ($one["tb_name"] ?? ""));
			if ($tb === "") {
				continue;
			}
			$menu = trim((string) ($one["menu_name"] ?? ""));
			$label = $tb;
			if ($menu !== "") {
				$label .= " (" . $menu . ")";
			}
			$opt[$tb] = $label;
		}
		return $opt;
	}

	private function get_table_id_options(Controller $ctl): array {
		$list = $ctl->db("db", "db")->getall("sort", SORT_ASC);
		$opt = ["" => $ctl->t("wizard.select_placeholder")];
		if (!is_array($list)) {
			return $opt;
		}
		foreach ($list as $one) {
			$id = $this->normalize_single_id($one["id"] ?? "");
			$tb = trim((string) ($one["tb_name"] ?? ""));
			$menu = trim((string) ($one["menu_name"] ?? ""));
			if ($id === "" || $tb === "") {
				continue;
			}
			$label = $tb;
			if ($menu !== "") {
				$label .= " (" . $menu . ")";
			}
			$opt[$id] = $label;
		}
		return $opt;
	}

	private function get_note_show_menu_options(): array {
		return [
			"0" => "Hide",
			"1" => "Show"
		];
	}

	private function get_note_sort_order_options(): array {
		return [
			"4" => "ASC",
			"3" => "DESC"
		];
	}

	private function get_dropdown_item_display_type_options(): array {
		return [
			"field" => "Field",
			"template" => "Multiple Fields (Template)"
		];
	}

	private function ensure_note_edit_sort_field(Controller $ctl, int $db_id): void {
		$where_fields = ["db_id", "parameter_name"];
		$where_values = [$db_id, "sort"];
		$list = $ctl->db("db_fields", "db")->select($where_fields, $where_values);
		if (is_array($list) && count($list) > 0) {
			return;
		}
		$insert_row = [
			"db_id" => $db_id,
			"type" => "number",
			"length" => 24,
			"parameter_name" => "sort",
			"parameter_title" => "Sort"
		];
		$ctl->db("db_fields", "db")->insert($insert_row);
	}

	private function ensure_note_edit_weekly_calendar_fields(Controller $ctl, int $db_id): void {
		$required = [
			[
				"parameter_name" => "datetime",
				"type" => "datetime",
				"length" => 15,
				"parameter_title" => "Scheduled Date & Time",
				"default_value" => ""
			],
			[
				"parameter_name" => "duration",
				"type" => "number",
				"length" => 24,
				"parameter_title" => "Duration(minutes)",
				"default_value" => 60
			],
			[
				"parameter_name" => "travel_before",
				"type" => "number",
				"length" => 24,
				"parameter_title" => "Travel Time Before (min)",
				"default_value" => 0
			],
			[
				"parameter_name" => "travel_after",
				"type" => "number",
				"length" => 24,
				"parameter_title" => "Travel Time After (min)",
				"default_value" => 0
			],
			[
				"parameter_name" => "status",
				"type" => "dropdown",
				"length" => 24,
				"parameter_title" => "Status",
				"default_value" => 0,
				"constant_array_name" => "workflows"
			]
		];
		foreach ($required as $def) {
			$list = $ctl->db("db_fields", "db")->select(["db_id", "parameter_name"], [$db_id, $def["parameter_name"]]);
			if (is_array($list) && count($list) > 0) {
				continue;
			}
			$ctl->db("db_fields", "db")->insert([
				"db_id" => $db_id,
				"type" => $def["type"],
				"length" => $def["length"],
				"parameter_name" => $def["parameter_name"],
				"parameter_title" => $def["parameter_title"],
				"validation" => 0,
				"default_value" => $def["default_value"],
				"constant_array_name" => $def["constant_array_name"] ?? ""
			]);
		}
	}

	private function get_cascade_delete_flag_options(): array {
		return [
			"0" => "Do not delete",
			"1" => "Cascade delete"
		];
	}

	private function get_note_list_type_options(): array {
		return [
			"0" => "Search and Table",
			"1" => "Manual Sort"
		];
	}

	private function get_note_side_list_type_options(): array {
		return [
			"0" => "Same as Main Screen",
			"1" => "Search and Table",
			"2" => "Manual Sort"
		];
	}

	private function get_note_toggle_options(): array {
		return [
			"0" => "Hide",
			"1" => "Show"
		];
	}

	private function get_note_parent_icon_options(): array {
		return [
			"0" => "Show",
			"1" => "Hide"
		];
	}

	private function get_note_sortkey_options(Controller $ctl, string $tb_name): array {
		$tb_name = $this->normalize_table_name($tb_name);
		$opt = ["id" => "ID"];
		if ($tb_name === "") {
			return $opt;
		}
		$db = $this->find_db_row_by_tb_name($ctl, $tb_name);
		if (!is_array($db) || count($db) === 0) {
			return $opt;
		}
		$db_id = $this->normalize_single_id($db["id"] ?? "");
		if ($db_id === "") {
			return $opt;
		}
		$list = $ctl->db("db_fields", "db")->select("db_id", $db_id, true, "AND", "sort", SORT_ASC);
		if (!is_array($list)) {
			return $opt;
		}
		foreach ($list as $one) {
			$field_name = trim((string) ($one["parameter_name"] ?? ""));
			$type = trim((string) ($one["type"] ?? ""));
			if ($field_name === "") {
				continue;
			}
			if ($type !== "text" && $type !== "number") {
				continue;
			}
			$opt[$field_name] = $field_name;
		}
		return $opt;
	}

	private function get_parent_dropdown_item_options(Controller $ctl, string $tb_name): array {
		$tb_name = $this->normalize_table_name($tb_name);
		$opt = ["id" => "ID"];
		if ($tb_name === "") {
			return $opt;
		}
		$db = $this->find_db_row_by_tb_name($ctl, $tb_name);
		if (!is_array($db) || count($db) === 0) {
			return $opt;
		}
		$db_id = $this->normalize_single_id($db["id"] ?? "");
		if ($db_id === "") {
			return $opt;
		}
		$list = $ctl->db("db_fields", "db")->select("db_id", $db_id, true, "AND", "sort", SORT_ASC);
		if (!is_array($list)) {
			return $opt;
		}
		foreach ($list as $one) {
			$field_name = trim((string) ($one["parameter_name"] ?? ""));
			$type = trim((string) ($one["type"] ?? ""));
			if ($field_name === "" || $field_name === "parent_id") {
				continue;
			}
			if ($type !== "text" && $type !== "number") {
				continue;
			}
			$opt[$field_name] = $field_name;
		}
		return $opt;
	}

	private function find_db_row_by_tb_name(Controller $ctl, string $tb_name): array {
		$tb_name = $this->normalize_table_name($tb_name);
		if ($tb_name === "") {
			return [];
		}
		$list = $ctl->db("db", "db")->select("tb_name", $tb_name);
		if (!is_array($list) || count($list) === 0) {
			return [];
		}
		return is_array($list[0]) ? $list[0] : [];
	}

	private function is_valid_note_width(string $width): bool {
		if ($width === "" || !ctype_digit($width)) {
			return false;
		}
		$val = (int) $width;
		return $val >= 600 && $val <= 1200;
	}

	private function is_valid_parent_child_side_width(string $width): bool {
		if ($width === "" || !ctype_digit($width)) {
			return false;
		}
		return (int) $width >= 100;
	}

	private function build_note_edit_plan_lines(array $row): array {
		$lines = [
			"1. 対象ノート: " . (string) ($row["target_tb_name"] ?? ""),
			"2. db の対象レコードを更新",
			"3. menu_name / description / show_menu / sortkey / sort_order / list_type / show_duplicate / show_id / edit_width を反映"
		];
		if ((int) ($row["has_parent_note"] ?? 0) > 0) {
			$lines[] = "4. side_list_type / cascade_delete_flag / show_icon_on_parent_list も反映";
			$lines[] = "5. db_tables_list / app_call / app_check で確認";
			return $lines;
		}
		$lines[] = "4. db_tables_list / app_call / app_check で確認";
		return $lines;
	}

	private function build_note_edit_prompt_text(array $row, array $plan_lines): string {
		$plan_block = "- " . implode("\n- ", $plan_lines);
		$change_lines = [
			"- menu_name: " . (string) ($row["menu_name"] ?? ""),
			"- description: " . (string) ($row["description"] ?? ""),
			"- show_menu: " . (string) ($row["show_menu_label"] ?? "") . " (" . (string) ($row["show_menu"] ?? "") . ")",
			"- sortkey: " . (string) ($row["sortkey"] ?? ""),
			"- sort_order: " . (string) ($row["sort_order_label"] ?? "") . " (" . (string) ($row["sort_order"] ?? "") . ")",
			"- list_type: " . (string) ($row["list_type_label"] ?? "") . " (" . (string) ($row["list_type"] ?? "") . ")",
			"- show_duplicate: " . (string) ($row["show_duplicate_label"] ?? "") . " (" . (string) ($row["show_duplicate"] ?? "") . ")",
			"- show_id: " . (string) ($row["show_id_label"] ?? "") . " (" . (string) ($row["show_id"] ?? "") . ")",
			"- edit_width: " . (string) ($row["edit_width"] ?? "")
		];
		if ((int) ($row["has_parent_note"] ?? 0) > 0) {
			$change_lines[] = "- side_list_type: " . (string) ($row["side_list_type_label"] ?? "") . " (" . (string) ($row["side_list_type"] ?? "") . ")";
			$change_lines[] = "- cascade_delete_flag: " . (string) ($row["cascade_delete_flag_label"] ?? "") . " (" . (string) ($row["cascade_delete_flag"] ?? "") . ")";
			$change_lines[] = "- show_icon_on_parent_list: " . (string) ($row["show_icon_on_parent_list_label"] ?? "") . " (" . (string) ($row["show_icon_on_parent_list"] ?? "") . ")";
		}
		return trim(
"【変更種別】\n" .
"ノートの設定変更\n\n" .
"【対象ノート】\n" .
(string) ($row["target_tb_name"] ?? "") . " (db_id=" . (string) ($row["db_id"] ?? "") . ")\n\n" .
"【変更後設定】\n" .
implode("\n", $change_lines) . "\n\n" .
"【実装方針】\n" .
"- db テーブルの対象レコードを更新する\n" .
"- 対象ノートの基本設定のみ変更する\n" .
"- schema(db_fields) 自体は変更しない\n" .
"- 変更後は一覧表示と編集ダイアログに破綻がないことを確認する\n\n" .
"【実行計画】\n" .
$plan_block . "\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- db_tables_list で更新内容を確認できる\n" .
"- app_call 成功\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_note_delete_plan_lines(array $row): array {
		return [
			"1. 対象ノート: " . (string) ($row["target_tb_name"] ?? ""),
			"2. 対象ノートの db レコードを特定",
			"3. 関連する db_fields / screen_fields / db_additionals / 実データを含めて削除",
			"4. 関連依存が残らないよう整理",
			"5. db_tables_list / data_list / app_check で削除確認"
		];
	}

	private function build_note_delete_prompt_text(array $row, array $plan_lines): string {
		$plan_block = "- " . implode("\n- ", $plan_lines);
		return trim(
"【変更種別】\n" .
"ノートの削除\n\n" .
"【対象ノート】\n" .
(string) ($row["target_tb_name"] ?? "") . " (db_id=" . (string) ($row["db_id"] ?? "") . ")\n\n" .
"【ノート名】\n" .
(string) ($row["menu_name"] ?? "") . "\n\n" .
"【説明】\n" .
(string) ($row["description"] ?? "") . "\n\n" .
"【削除方針】\n" .
"- 対象ノートの定義だけでなく関連データも含めて削除する\n" .
"- db / db_fields / screen_fields / db_additionals / 実データ / 関連する不要ファイルを整理する\n" .
"- 依存関係があれば確認し、削除後に不整合が残らないようにする\n\n" .
"【実行計画】\n" .
$plan_block . "\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- db_tables_list で対象ノートが消えている\n" .
"- 関連データが残っていないことを確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_table_change_add_plan_lines(array $row): array {
		$field_lines = $this->normalize_field_lines((string) ($row["fields_text"] ?? ""));
		$lines = [];
		$lines[] = "1. 対象テーブル: " . $row["target_tb_name"];
		$lines[] = "2. db_fieldsへ追加項目を登録";
		$lines[] = "3. 画面反映: 項目ごとの指定に合わせて設定";
		$display_lines = $this->build_display_matrix_lines((string) ($row["fields_text"] ?? ""), $row["display_matrix"] ?? null);
		foreach ($display_lines as $dline) {
			$lines[] = "   - " . $dline;
		}
		$lines[] = "   - listを選択した項目はlist_on_sideにも反映";
		$lines[] = "4. app_call / data_get / app_checkで確認";
		if (!empty($field_lines)) {
			$lines[] = "5. 追加候補";
			foreach ($field_lines as $one) {
				$lines[] = "   - " . $one;
			}
		}
		return $lines;
	}

	private function build_table_change_add_prompt_text(array $row, array $plan_lines): string {
		$field_lines = $this->normalize_field_lines((string) ($row["fields_text"] ?? ""));
		$field_block = "- " . implode("\n- ", $field_lines);
		$display_text = $this->build_display_matrix_text((string) ($row["fields_text"] ?? ""), $row["display_matrix"] ?? null);
		$plan_block = "- " . implode("\n- ", $plan_lines);

		return trim(
"【変更種別】\n" .
"テーブルの変更 / 項目の追加\n\n" .
"【対象テーブル】\n" .
$row["target_tb_name"] . "\n\n" .
"【追加したい項目（human-readable）】\n" .
$field_block . "\n\n" .
"【表示先】\n" .
$display_text . "\n" .
"(listを選択した項目は list_on_side にも反映)\n\n" .
"【実行計画】\n" .
$plan_block . "\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 更新内容が data_get または data_list で確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_table_change_delete_plan_lines(array $row): array {
		$field_lines = $this->normalize_field_lines((string) ($row["fields_text"] ?? ""));
		$lines = [];
		$lines[] = "1. 対象テーブル: " . $row["target_tb_name"];
		$lines[] = "2. 削除対象のdb_fieldsを特定";
		if (!empty($field_lines)) {
			$lines[] = "3. 削除対象";
			foreach ($field_lines as $one) {
				$lines[] = "   - " . $one;
			}
		}
		$lines[] = "4. 関連するscreen_fieldsを整理（表示から除外）";
		$lines[] = "5. app_call / data_get / app_checkで確認";
		return $lines;
	}

	private function build_table_change_delete_prompt_text(array $row, array $plan_lines): string {
		$field_lines = $this->normalize_field_lines((string) ($row["fields_text"] ?? ""));
		$field_block = "- " . implode("\n- ", $field_lines);
		$plan_block = "- " . implode("\n- ", $plan_lines);

		return trim(
"【変更種別】\n" .
"テーブルの変更 / 項目の削除\n\n" .
"【対象テーブル】\n" .
$row["target_tb_name"] . "\n\n" .
"【削除したい項目】\n" .
$field_block . "\n\n" .
"【実行計画】\n" .
$plan_block . "\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 更新内容が data_get または data_list で確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_table_change_update_plan_lines(array $row): array {
		$lines = [];
		$lines[] = "1. 対象テーブル: " . $row["target_tb_name"];
		$lines[] = "2. 変更対象の項目を特定";
		$lines[] = "   - " . (string) ($row["fields_text"] ?? "");
		$lines[] = "3. 変更内容";
		$change_lines = $this->normalize_field_lines((string) ($row["update_field_change_text"] ?? ""));
		if (count($change_lines) === 0) {
			$change_lines = [(string) ($row["update_field_change_text"] ?? "")];
		}
		foreach ($change_lines as $one) {
			$lines[] = "   - " . $one;
		}
		$lines[] = "4. 関連するscreen_fields/db_additionalsへの影響確認";
		$lines[] = "5. app_call / data_get / app_checkで確認";
		return $lines;
	}

	private function build_table_change_update_prompt_text(array $row, array $plan_lines): string {
		$plan_block = "- " . implode("\n- ", $plan_lines);

		return trim(
"【変更種別】\n" .
"テーブルの変更 / 項目の変更\n\n" .
"【対象テーブル】\n" .
$row["target_tb_name"] . "\n\n" .
"【変更対象項目】\n" .
(string) ($row["fields_text"] ?? "") . "\n\n" .
"【変更内容】\n" .
(string) ($row["update_field_change_text"] ?? "") . "\n\n" .
"【実行計画】\n" .
$plan_block . "\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 更新内容が data_get または data_list で確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_table_change_add_screen_plan_lines(array $row): array {
		$field_lines = $this->normalize_field_lines((string) ($row["fields_text"] ?? ""));
		$lines = [];
		$lines[] = "1. 対象テーブル: " . $row["target_tb_name"];
		$lines[] = "2. 対象の既存項目を特定";
		foreach ($field_lines as $one) {
			$lines[] = "   - " . $one;
		}
		$lines[] = "3. screen_fieldsへ表示先ごとに追加・削除を反映";
		$display_lines = $this->build_screen_display_matrix_lines((string) ($row["fields_text"] ?? ""), $row["display_matrix"] ?? null);
		foreach ($display_lines as $dline) {
			$lines[] = "   - " . $dline;
		}
		$lines[] = "4. app_call / data_get / app_checkで確認";
		return $lines;
	}

	private function build_table_change_add_screen_prompt_text(array $row, array $plan_lines): string {
		$field_lines = $this->normalize_field_lines((string) ($row["fields_text"] ?? ""));
		$field_block = "- " . implode("\n- ", $field_lines);
		$display_text = $this->build_screen_display_matrix_text((string) ($row["fields_text"] ?? ""), $row["display_matrix"] ?? null);
		$plan_block = "- " . implode("\n- ", $plan_lines);

		return trim(
"【変更種別】\n" .
"テーブルの変更 / 画面への項目表示設定\n\n" .
"【対象テーブル】\n" .
$row["target_tb_name"] . "\n\n" .
"【対象項目】\n" .
$field_block . "\n\n" .
"【表示先】\n" .
$display_text . "\n\n" .
"【実行計画】\n" .
$plan_block . "\n\n" .
$this->build_prompt_policy_block() . "\n\n" .
"【完了条件】\n" .
"- app_call 成功\n" .
"- 更新内容が data_get または data_list で確認できる\n" .
"- 主要導線を app_check で確認\n\n" .
"実装後は、変更ファイル一覧・検証コマンド・確認結果を簡潔に報告してください。"
		);
	}

	private function build_prompt_policy_block(): string {
		return "【実装方針・制約】\n" .
			"- AGENTS.md / SKILL.md に従って実装する。\n" .
			"- fbp/ 以下は変更しない。";
	}

	private function normalize_field_lines(string $fields_text): array {
		$lines = preg_split('/\r\n|\r|\n/', $fields_text);
		if (!is_array($lines)) {
			return [];
		}
		$res = [];
		foreach ($lines as $line) {
			$one = trim((string) $line);
			if ($one === "") {
				continue;
			}
			$res[] = $one;
		}
		return $res;
	}

	private function normalize_fields_text_for_human(string $fields_text): string {
		$lines = $this->normalize_field_lines($fields_text);
		$res = [];
		foreach ($lines as $line) {
			// Legacy format: field_name:type:label -> label（type）
			if (strpos($line, ":") !== false) {
				$parts = explode(":", $line);
				if (count($parts) >= 3) {
					$type = trim((string) ($parts[1] ?? "text"));
					$label = trim((string) implode(":", array_slice($parts, 2)));
					if ($label !== "") {
						$res[] = $label . "（" . $type . "）";
						continue;
					}
				}
			}
			$res[] = $line;
		}
		return implode("\n", $res);
	}

	private function find_duplicate_in_input_fields(string $fields_text): string {
		$lines = $this->normalize_field_lines($fields_text);
		$seen_title = [];
		$seen_field_name = [];
		foreach ($lines as $line) {
			$meta = $this->parse_field_input_line($line);
			$title_key = $this->normalize_compare_key((string) $meta["title"]);
			if ($title_key !== "") {
				if (isset($seen_title[$title_key])) {
					return (string) $meta["title"];
				}
				$seen_title[$title_key] = 1;
			}
			$field_name_key = strtolower(trim((string) $meta["field_name"]));
			if ($field_name_key !== "") {
				if (isset($seen_field_name[$field_name_key])) {
					return (string) $meta["field_name"];
				}
				$seen_field_name[$field_name_key] = 1;
			}
		}
		return "";
	}

	private function find_existing_field_conflict(Controller $ctl, string $tb_name, string $fields_text): string {
		$tb_name = $this->normalize_table_name($tb_name);
		if ($tb_name === "") {
			return "";
		}
		$db_list = $ctl->db("db", "db")->select("tb_name", $tb_name);
		if (!is_array($db_list) || count($db_list) === 0) {
			return "";
		}
		$db_id = (string) ($db_list[0]["id"] ?? "");
		if ($db_id === "") {
			return "";
		}
		$existing = $ctl->db("db_fields", "db")->select("db_id", $db_id, true, "AND", "sort", SORT_ASC);
		if (!is_array($existing) || count($existing) === 0) {
			return "";
		}
		$existing_title = [];
		$existing_field_name = [];
		foreach ($existing as $row) {
			$title_key = $this->normalize_compare_key((string) ($row["parameter_title"] ?? ""));
			if ($title_key !== "") {
				$existing_title[$title_key] = 1;
			}
			$field_name_key = strtolower(trim((string) ($row["parameter_name"] ?? "")));
			if ($field_name_key !== "") {
				$existing_field_name[$field_name_key] = 1;
			}
		}

		$lines = $this->normalize_field_lines($fields_text);
		foreach ($lines as $line) {
			$meta = $this->parse_field_input_line($line);
			$field_name_key = strtolower(trim((string) $meta["field_name"]));
			if ($field_name_key !== "" && isset($existing_field_name[$field_name_key])) {
				return (string) $meta["field_name"];
			}
			$title_key = $this->normalize_compare_key((string) $meta["title"]);
			if ($title_key !== "" && isset($existing_title[$title_key])) {
				return (string) $meta["title"];
			}
		}
		return "";
	}

	private function parse_field_input_line(string $line): array {
		$raw = trim($line);
		$field_name = "";
		$title = $raw;

		if (substr_count($raw, ":") >= 2) {
			$parts = explode(":", $raw);
			$field_name = $this->normalize_table_name((string) ($parts[0] ?? ""));
			$label = trim((string) implode(":", array_slice($parts, 2)));
			if ($label !== "") {
				$title = $label;
			}
		}
		$title = preg_replace('/[（(].*$/u', '', $title);
		$title = trim((string) $title);
		if ($title === "") {
			$title = $raw;
		}
		return [
			"field_name" => $field_name,
			"title" => $title
		];
	}

	private function normalize_compare_key(string $src): string {
		$val = mb_convert_kana($src, "asKV");
		$val = mb_strtolower($val, "UTF-8");
		$val = preg_replace('/\s+/u', '', $val);
		return trim((string) $val);
	}

	private function normalize_id_list($src): array {
		if (!is_array($src)) {
			return [];
		}
		$res = [];
		foreach ($src as $v) {
			$id = (string) $v;
			if ($id === "" || !preg_match('/^[0-9]+$/', $id)) {
				continue;
			}
			$res[$id] = 1;
		}
		return array_keys($res);
	}

	private function normalize_single_id($src): string {
		$id = (string) $src;
		if ($id === "" || !preg_match('/^[0-9]+$/', $id)) {
			return "";
		}
		return $id;
	}

	private function default_display_targets(): array {
		return [
			"search" => 1,
			"list" => 1,
			"add" => 1,
			"edit" => 1,
			"delete" => 1,
			"list_on_side" => 0
		];
	}

	private function normalize_display_targets($src): array {
		$def = $this->default_display_targets();
		if (!is_array($src)) {
			return $def;
		}
		$res = [];
		foreach ($def as $key => $v) {
			$res[$key] = !empty($src[$key]) ? 1 : 0;
		}
		return $res;
	}

	private function build_display_targets_from_post(Controller $ctl): array {
		$post = $ctl->POST("display_targets");
		return $this->normalize_display_targets($post);
	}

	private function build_display_targets_text($src): string {
		$targets = $this->normalize_display_targets($src);
		$labels = [
			"list" => "list",
			"add" => "add",
			"edit" => "edit",
			"delete" => "delete",
			"search" => "search",
			"list_on_side" => "list_on_side"
		];
		$enabled = [];
		foreach ($labels as $key => $label) {
			if (!empty($targets[$key])) {
				$enabled[] = $label;
			}
		}
		if (count($enabled) === 0) {
			return "（なし）";
		}
		return implode(", ", $enabled);
	}

	private function get_display_target_labels(): array {
		return [
			"search" => "search",
			"list" => "list",
			"add" => "add",
			"edit" => "edit",
			"delete" => "delete"
		];
	}

	private function get_screen_display_target_labels(): array {
		return [
			"search" => "search",
			"list" => "list",
			"add" => "add",
			"edit" => "edit",
			"delete" => "delete",
			"list_on_side" => "list_on_side"
		];
	}

	private function build_default_display_matrix(string $fields_text): array {
		$lines = $this->normalize_field_lines($fields_text);
		return $this->normalize_display_matrix([], $lines, true);
	}

	private function build_empty_display_matrix(string $fields_text): array {
		$lines = $this->normalize_field_lines($fields_text);
		$res = [];
		foreach ($lines as $idx => $line) {
			$res[$idx] = [
				"search" => 0,
				"list" => 0,
				"add" => 0,
				"edit" => 0,
				"delete" => 0,
				"list_on_side" => 0
			];
		}
		return $res;
	}

	private function normalize_display_matrix($src, array $field_lines, bool $link_list_on_side = true): array {
		$res = [];
		foreach ($field_lines as $idx => $line) {
			$row = [];
			if (is_array($src) && isset($src[$idx]) && is_array($src[$idx])) {
				$row = $src[$idx];
			}
			$one = $this->normalize_display_targets($row);
			if ($link_list_on_side) {
				$one["list_on_side"] = !empty($one["list"]) ? 1 : 0;
			}
			$res[$idx] = $one;
		}
		return $res;
	}

	private function build_display_matrix_from_post(Controller $ctl, string $fields_text, bool $link_list_on_side = true): array {
		$post = $ctl->POST("display_matrix");
		$lines = $this->normalize_field_lines($fields_text);
		return $this->normalize_display_matrix($post, $lines, $link_list_on_side);
	}

	private function normalize_screen_display_matrix($src, array $field_lines): array {
		$res = [];
		foreach ($field_lines as $idx => $line) {
			$row = [];
			if (is_array($src) && isset($src[$idx]) && is_array($src[$idx])) {
				$row = $src[$idx];
			}
			$one = $this->normalize_display_targets($row);
			$res[$idx] = $one;
		}
		return $res;
	}

	private function build_screen_display_matrix_from_post(Controller $ctl, string $fields_text): array {
		$post = $ctl->POST("display_matrix");
		$lines = $this->normalize_field_lines($fields_text);
		return $this->normalize_screen_display_matrix($post, $lines);
	}

	private function build_field_display_rows(array $state): array {
		$fields_text = (string) ($state["fields_text"] ?? "");
		$lines = $this->normalize_field_lines($fields_text);
		$matrix = $this->normalize_display_matrix($state["display_matrix"] ?? null, $lines, true);
		$rows = [];
		foreach ($lines as $idx => $line) {
			$meta = $this->parse_field_input_line($line);
			$title = trim((string) ($meta["title"] ?? ""));
			if ($title === "") {
				$title = trim((string) $line);
			}
			$rows[] = [
				"idx" => (int) $idx,
				"title" => $title,
				"targets" => $matrix[$idx] ?? $this->default_display_targets()
			];
		}
		return $rows;
	}

	private function build_display_matrix_lines(string $fields_text, $display_matrix): array {
		$lines = $this->normalize_field_lines($fields_text);
		$matrix = $this->normalize_display_matrix($display_matrix, $lines, true);
		$labels = $this->get_display_target_labels();
		$res = [];
		foreach ($lines as $idx => $line) {
			$meta = $this->parse_field_input_line($line);
			$title = trim((string) ($meta["title"] ?? ""));
			if ($title === "") {
				$title = trim((string) $line);
			}
			$enabled = [];
			foreach ($labels as $key => $label) {
				if (!empty($matrix[$idx][$key])) {
					$enabled[] = $label;
				}
			}
			$target_text = count($enabled) > 0 ? implode(", ", $enabled) : "（なし）";
			$res[] = $title . " : " . $target_text;
		}
		return $res;
	}

	private function build_screen_display_matrix_from_existing(Controller $ctl, string $tb_name, string $fields_text): array {
		$lines = $this->normalize_field_lines($fields_text);
		$matrix = $this->normalize_screen_display_matrix([], $lines);
		$labels = $this->get_screen_display_target_labels();
		foreach ($lines as $idx => $line) {
			$pname = $this->extract_parameter_name_from_label($line);
			if ($pname === "") {
				continue;
			}
			$rows = $ctl->db("screen_fields", "db")->select(["tb_name", "parameter_name"], [$tb_name, $pname], true, "AND", "screen_name", SORT_ASC);
			if (!is_array($rows)) {
				continue;
			}
			foreach ($rows as $r) {
				$screen_name = trim((string) ($r["screen_name"] ?? ""));
				if ($screen_name !== "" && isset($labels[$screen_name])) {
					$matrix[$idx][$screen_name] = 1;
				}
			}
		}
		return $matrix;
	}

	private function build_screen_field_display_rows(array $state): array {
		$fields_text = (string) ($state["fields_text"] ?? "");
		$lines = $this->normalize_field_lines($fields_text);
		$matrix = $this->normalize_screen_display_matrix($state["display_matrix"] ?? null, $lines);
		$rows = [];
		foreach ($lines as $idx => $line) {
			$meta = $this->parse_field_input_line($line);
			$title = trim((string) ($meta["title"] ?? ""));
			if ($title === "") {
				$title = trim((string) $line);
			}
			$rows[] = [
				"idx" => (int) $idx,
				"title" => $title,
				"targets" => $matrix[$idx] ?? $this->default_display_targets()
			];
		}
		return $rows;
	}

	private function build_screen_display_matrix_lines(string $fields_text, $display_matrix): array {
		$lines = $this->normalize_field_lines($fields_text);
		$matrix = $this->normalize_screen_display_matrix($display_matrix, $lines);
		$labels = $this->get_screen_display_target_labels();
		$res = [];
		foreach ($lines as $idx => $line) {
			$meta = $this->parse_field_input_line($line);
			$title = trim((string) ($meta["title"] ?? ""));
			if ($title === "") {
				$title = trim((string) $line);
			}
			$enabled = [];
			foreach ($labels as $key => $label) {
				if (!empty($matrix[$idx][$key])) {
					$enabled[] = $label;
				}
			}
			$target_text = count($enabled) > 0 ? implode(", ", $enabled) : "（なし）";
			$res[] = $title . " : " . $target_text;
		}
		return $res;
	}

	private function build_screen_display_matrix_text(string $fields_text, $display_matrix): string {
		$lines = $this->build_screen_display_matrix_lines($fields_text, $display_matrix);
		if (count($lines) === 0) {
			return "（なし）";
		}
		return implode("\n", $lines);
	}

	private function reorder_display_payload(string $fields_text, $display_matrix, string $display_order): array {
		$lines = $this->normalize_field_lines($fields_text);
		$matrix = $this->normalize_screen_display_matrix($display_matrix, $lines);
		if ($display_order === "") {
			return [
				"fields_text" => implode("\n", $lines),
				"display_matrix" => array_values($matrix)
			];
		}
		$parts = explode(",", $display_order);
		$order = [];
		$seen = [];
		$max_idx = count($lines) - 1;
		foreach ($parts as $one) {
			$idx = (int) trim((string) $one);
			if ($idx < 0 || $idx > $max_idx) {
				continue;
			}
			if (isset($seen[$idx])) {
				continue;
			}
			$seen[$idx] = 1;
			$order[] = $idx;
		}
		for ($i = 0; $i <= $max_idx; $i++) {
			if (!isset($seen[$i])) {
				$order[] = $i;
			}
		}
		$new_lines = [];
		$new_matrix = [];
		foreach ($order as $idx) {
			$new_lines[] = (string) ($lines[$idx] ?? "");
			$new_matrix[] = $matrix[$idx] ?? $this->default_display_targets();
		}
		return [
			"fields_text" => implode("\n", $new_lines),
			"display_matrix" => $new_matrix
		];
	}

	private function replace_screen_fields_setting(Controller $ctl, array $state): void {
		$tb_name = $this->normalize_table_name((string) ($state["target_tb_name"] ?? ""));
		if ($tb_name === "") {
			return;
		}
		$fields_text = (string) ($state["fields_text"] ?? "");
		$lines = $this->normalize_field_lines($fields_text);
		$matrix = $this->normalize_screen_display_matrix($state["display_matrix"] ?? null, $lines);
		$screen_names = array_keys($this->get_screen_display_target_labels());

		$db_list = $ctl->db("db", "db")->select("tb_name", $tb_name);
		if (!is_array($db_list) || count($db_list) === 0) {
			return;
		}
		$db_id = (string) ($db_list[0]["id"] ?? "");
		if ($db_id === "") {
			return;
		}
		$field_list = $ctl->db("db_fields", "db")->select("db_id", $db_id, true, "AND", "sort", SORT_ASC);
		$field_map = [];
		if (is_array($field_list)) {
			foreach ($field_list as $f) {
				$pname = trim((string) ($f["parameter_name"] ?? ""));
				$id = (string) ($f["id"] ?? "");
				if ($pname !== "" && $id !== "") {
					$field_map[$pname] = $id;
				}
			}
		}

		foreach ($screen_names as $screen_name) {
			$existing = $ctl->db("screen_fields", "db")->select(["tb_name", "screen_name"], [$tb_name, $screen_name], true, "AND", "sort", SORT_ASC);
			if (is_array($existing)) {
				foreach ($existing as $row) {
					$rid = (string) ($row["id"] ?? "");
					if ($rid !== "") {
						$ctl->db("screen_fields", "db")->delete($rid);
					}
				}
			}
		}

		$sort_map = [];
		foreach ($screen_names as $screen_name) {
			$sort_map[$screen_name] = 1;
		}
		foreach ($lines as $idx => $line) {
			$pname = $this->extract_parameter_name_from_label($line);
			if ($pname === "" || !isset($field_map[$pname])) {
				continue;
			}
			foreach ($screen_names as $screen_name) {
				if (empty($matrix[$idx][$screen_name])) {
					continue;
				}
				$arr = [
					"tb_name" => $tb_name,
					"screen_name" => $screen_name,
					"parameter_name" => $pname,
					"db_fields_id" => $field_map[$pname],
					"sort" => $sort_map[$screen_name]
				];
				$ctl->db("screen_fields", "db")->insert($arr);
				$sort_map[$screen_name]++;
			}
		}
	}

	private function extract_parameter_name_from_label(string $label): string {
		if (preg_match('/\\[([a-zA-Z0-9_]+)\\]\\s*$/u', $label, $m)) {
			return (string) ($m[1] ?? "");
		}
		return "";
	}

	private function build_display_matrix_text(string $fields_text, $display_matrix): string {
		$lines = $this->build_display_matrix_lines($fields_text, $display_matrix);
		if (count($lines) === 0) {
			return "（なし）";
		}
		return implode("\n", $lines);
	}

	private function get_table_field_options(Controller $ctl, string $tb_name): array {
		$tb_name = $this->normalize_table_name($tb_name);
		if ($tb_name === "") {
			return [];
		}
		$db_list = $ctl->db("db", "db")->select("tb_name", $tb_name);
		if (!is_array($db_list) || count($db_list) === 0) {
			return [];
		}
		$db_id = (string) ($db_list[0]["id"] ?? "");
		if ($db_id === "") {
			return [];
		}
		$list = $ctl->db("db_fields", "db")->select("db_id", $db_id, true, "AND", "sort", SORT_ASC);
		if (!is_array($list)) {
			return [];
		}
		$res = [];
		foreach ($list as $f) {
			$id = (string) ($f["id"] ?? "");
			$pname = trim((string) ($f["parameter_name"] ?? ""));
			$ptitle = trim((string) ($f["parameter_title"] ?? ""));
			$type = trim((string) ($f["type"] ?? ""));
			if ($id === "" || $pname === "") {
				continue;
			}
			if (in_array($pname, ["id", "parent_id", "created_at", "updated_at"], true)) {
				continue;
			}
			$label = $ptitle !== "" ? $ptitle : $pname;
			if ($type !== "") {
				$label .= "（" . $type . "）";
			}
			$label .= " [" . $pname . "]";
			$res[$id] = $label;
		}
		return $res;
	}

	private function get_table_field_detail_rows(Controller $ctl, string $tb_name): array {
		$tb_name = $this->normalize_table_name($tb_name);
		if ($tb_name === "") {
			return [];
		}
		$db_list = $ctl->db("db", "db")->select("tb_name", $tb_name);
		if (!is_array($db_list) || count($db_list) === 0) {
			return [];
		}
		$db_id = (string) ($db_list[0]["id"] ?? "");
		if ($db_id === "") {
			return [];
		}
		$list = $ctl->db("db_fields", "db")->select("db_id", $db_id, true, "AND", "sort", SORT_ASC);
		if (!is_array($list)) {
			return [];
		}
		$res = [];
		foreach ($list as $f) {
			$id = (string) ($f["id"] ?? "");
			$field_name = trim((string) ($f["parameter_name"] ?? ""));
			if ($id === "" || $field_name === "") {
				continue;
			}
			if (in_array($field_name, ["id", "parent_id", "created_at", "updated_at"], true)) {
				continue;
			}
			$title = trim((string) ($f["parameter_title"] ?? ""));
			if ($title === "") {
				$title = $field_name;
			}
			$options_text = $this->build_constant_array_options_text($ctl, (string) ($f["constant_array_name"] ?? ""));
			$res[] = [
				"id" => $id,
				"field_name" => $field_name,
				"title" => $title,
				"options_text" => $options_text
			];
		}
		return $res;
	}

	private function build_constant_array_options_text(Controller $ctl, string $constant_array_name): string {
		$constant_array_name = trim($constant_array_name);
		if ($constant_array_name === "") {
			return "-";
		}
		try {
			$option_arr = $ctl->get_constant_array($constant_array_name, false);
			if (!is_array($option_arr) || count($option_arr) === 0) {
				return "-";
			}
			$parts = [];
			$count = 0;
			foreach ($option_arr as $key => $value) {
				$parts[] = (string) $key . ":" . (string) $value;
				$count++;
				if ($count >= 20) {
					break;
				}
			}
			if (count($parts) === 0) {
				return "-";
			}
			$text = implode(" ", $parts);
			if (count($option_arr) > 20) {
				$text .= " ...";
			}
			return $text;
		} catch (Throwable $e) {
			return "-";
		}
	}

	private function build_pdf_field_rows(array $rows, array $selected_ids): array {
		$sel = [];
		foreach ($selected_ids as $id) {
			$sel[(string) $id] = 1;
		}
		$res = [];
		foreach ($rows as $one) {
			$id = (string) ($one["id"] ?? "");
			if ($id === "") {
				continue;
			}
			$res[] = [
				"id" => $id,
				"field_name" => (string) ($one["field_name"] ?? ""),
				"title" => (string) ($one["title"] ?? ""),
				"options_text" => (string) ($one["options_text"] ?? "-"),
				"selected" => !empty($sel[$id]) ? 1 : 0
			];
		}
		return $res;
	}

	private function build_pdf_selected_field_rows(array $rows, array $selected_ids): array {
		$sel = [];
		foreach ($selected_ids as $id) {
			$sel[(string) $id] = 1;
		}
		$res = [];
		foreach ($rows as $one) {
			$id = (string) ($one["id"] ?? "");
			if ($id === "" || empty($sel[$id])) {
				continue;
			}
			$res[] = [
				"field_name" => (string) ($one["field_name"] ?? ""),
				"title" => (string) ($one["title"] ?? ""),
				"options_text" => (string) ($one["options_text"] ?? "-")
			];
		}
		return $res;
	}


	private function build_delete_field_rows(array $options, array $selected_ids): array {
		$selected_map = [];
		foreach ($selected_ids as $id) {
			$selected_map[(string) $id] = 1;
		}
		$rows = [];
		foreach ($options as $id => $label) {
			$key = (string) $id;
			$rows[] = [
				"id" => $key,
				"label" => $label,
				"selected" => !empty($selected_map[$key]) ? 1 : 0
			];
		}
		return $rows;
	}
}

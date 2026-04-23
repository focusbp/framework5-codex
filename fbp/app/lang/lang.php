<?php

/*
 *  YOU CAN'T CHANGE THIS PROJECT.
 *  It will be overwritten when the framework updates.
 */

class lang {

	private $ffm;

	function __construct(Controller $ctl) {
		$this->ffm = $ctl->db("lang");
		$ctl->set_check_login(false);
	}

	// Ajaxで取得する関数
	function list(Controller $ctl) {
		$list = $this->ffm->getall("en", SORT_ASC);

		$newlist = array();
		foreach ($list as $d) {
			$newlist[$d["classname"]][$d["en"]] = $d;
		}

		$defaults = [
			["classname" => "db_exe", "en" => "Add", "jp" => "追加"],
			["classname" => "db_exe", "en" => "Update", "jp" => "更新"],
			["classname" => "db_exe", "en" => "Delete", "jp" => "削除"],
			["classname" => "db_exe", "en" => "Search", "jp" => "検索"],
			["classname" => "db_exe", "en" => "Show Unassigned Tasks", "jp" => "フリータスク"],
			["classname" => "db_exe", "en" => "Jump", "jp" => "移動"],
			["classname" => "db_exe", "en" => "January", "jp" => "１月"],
			["classname" => "db_exe", "en" => "February", "jp" => "２月"],
			["classname" => "db_exe", "en" => "March", "jp" => "３月"],
			["classname" => "db_exe", "en" => "April", "jp" => "４月"],
			["classname" => "db_exe", "en" => "May", "jp" => "５月"],
			["classname" => "db_exe", "en" => "June", "jp" => "６月"],
			["classname" => "db_exe", "en" => "July", "jp" => "７月"],
			["classname" => "db_exe", "en" => "August", "jp" => "８月"],
			["classname" => "db_exe", "en" => "September", "jp" => "９月"],
			["classname" => "db_exe", "en" => "October", "jp" => "１０月"],
			["classname" => "db_exe", "en" => "November", "jp" => "１１月"],
			["classname" => "db_exe", "en" => "December", "jp" => "１２月"],
			["classname" => "db_exe", "en" => "Sun", "jp" => "日"],
			["classname" => "db_exe", "en" => "Mon", "jp" => "月"],
			["classname" => "db_exe", "en" => "Tue", "jp" => "火"],
			["classname" => "db_exe", "en" => "Wed", "jp" => "水"],
			["classname" => "db_exe", "en" => "Thu", "jp" => "木"],
			["classname" => "db_exe", "en" => "Fri", "jp" => "金"],
			["classname" => "db_exe", "en" => "Sat", "jp" => "土"],
		];
		foreach ($defaults as $def) {
			$force_classname = $def["classname"];
			$force_en = $def["en"];
			$force_jp = $def["jp"];
			$force_row = $newlist[$force_classname][$force_en] ?? null;
			if (empty($force_row) || empty($force_row["jp"])) {
				$list_force = $this->ffm->select(["classname", "en"], [$force_classname, $force_en]);
				if (count($list_force) > 0) {
					$d = $list_force[0];
					$d["jp"] = $force_jp;
					$this->ffm->update($d);
				} else {
					$d = array();
					$d["classname"] = $force_classname;
					$d["en"] = $force_en;
					$d["jp"] = $force_jp;
					$this->ffm->insert($d);
				}
				$newlist[$force_classname][$force_en] = [
					"classname" => $force_classname,
					"en" => $force_en,
					"jp" => $force_jp,
				];
			}
		}

		$ctl->append_res_data("list", $newlist);
	}
}

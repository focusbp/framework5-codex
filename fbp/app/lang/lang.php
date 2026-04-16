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

	function csv_download(Controller $ctl) {

		$list = $this->ffm->getall();
		$csv_data = [];
		foreach ($list as $key => $d) {
			$row = ['en' => $d["en"], 'jp' => $d["jp"]];
			$ctl->res_csv($row, 'sjis-win');
		}
	}

	function csv_upload(Controller $ctl) {

		if ($ctl->is_posted_file('csvfile')) {

			//save uploaded file as csvdata.csv
			$ctl->save_posted_file('csvfile', 'csvdata.csv');

			//get saved file path
			$filepath = $ctl->get_saved_filepath('csvdata.csv');

			//open saved file
			$fp = fopen($filepath, 'r');

			//set encoding for japanese
			stream_filter_register('convert.mbstring.*', 'Stream_Filter_Mbstring');
			$filtername = 'convert.mbstring.encoding.SJIS-win:UTF-8';
			stream_filter_append($fp, $filtername, STREAM_FILTER_READ);

			//read each line as csv
			$txt = "";
			$counter = 0;
			while (($row = fgetcsv($fp, 0, ",", "\"", "")) !== false) {
				$clist = $this->ffm->select(["en"], [$row[0]], true);
				if (count($clist) > 0) {
					$counter++;
					$clist[0]["jp"] = $row[1];
					$this->ffm->update($clist[0]);
				} else {
					if (!(empty($row[1]) && empty($row[0]))) {
						$newdata = array();
						$newdata["en"] = $row[0];
						$newdata["jp"] = $row[1];
						$this->ffm->insert($newdata);
					}
					$counter++;
				}
			}

			//close file
			fclose($fp);

			//response csv data to the view
			$ctl->assign("csvresult", $counter . " datas are updated/inserted.");
		}

		$this->edit($ctl);
	}

	function delete(Controller $ctl) {
		$id = $ctl->POST("id");
		$this->ffm->delete($id);

		$ctl->ajax("lang", "showlist");
	}

	function showlist(Controller $ctl) {
		$lang_search = $ctl->get_session("lang_search");

		$max = $ctl->increment_post_value("max", 1000);
		$list = $this->ffm->filter(["classname", "en", "jp"], [$lang_search, $lang_search, $lang_search], false, 'OR', 'en', SORT_ASC, $max, $is_last);
		$ctl->assign("max", $max);
		$ctl->assign("is_last", $is_last);

		foreach ($list as $k => $d) {
			if (empty($d["jp"])) {
				$this->ffm->delete($d["id"]);
				unset($list[$k]);
			}
		}

		$ctl->assign("list", $list);

		$ctl->reload_area("#tabs-translate", "index.tpl");
	}

	function search(Controller $ctl) {
		$post = $ctl->POST();

		$ctl->set_session("lang_search", $post["lang_search"] ?? null);

		$ctl->ajax("lang", "showlist");
	}

	function add(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign("post", $post);
		$ctl->show_multi_dialog("add_lang", "add.tpl", "Add Language", 700, true, true);
	}

	function add_exe(Controller $ctl) {
		$classname = trim((string) $ctl->POST("classname"));
		$en = trim((string) $ctl->POST("en"));
		$jp = trim((string) $ctl->POST("jp"));

		if ($classname === "" || $en === "" || $jp === "") {
			$ctl->show_notification_text("Class / English / Japanese are required.");
			return;
		}

		$list = $this->ffm->select(["classname", "en"], [$classname, $en]);
		if (count($list) > 0) {
			$d = $list[0];
			$d["jp"] = $jp;
			$this->ffm->update($d);
		} else {
			$d = array();
			$d["classname"] = $classname;
			$d["en"] = $en;
			$d["jp"] = $jp;
			$this->ffm->insert($d);
		}

		$ctl->close_multi_dialog("add_lang");
		$ctl->ajax("lang", "showlist");
	}

	function update(Controller $ctl) {
		$classname = $ctl->POST("classname");
		$en = $ctl->POST("en");
		$jp = $ctl->POST("jp");

		$list = $this->ffm->select(["classname", "en"], [$classname, $en]);

		if (!empty($jp)) {
			if (count($list) == 0) {
				$d = array();
				$d["classname"] = $classname;
				$d["en"] = $en;
				$d["jp"] = $jp;
				$this->ffm->insert($d);
			} else {
				$d = $list[0];
				$d["jp"] = $jp;
				$this->ffm->update($d);
			}
		} else {
			foreach ($list as $d) {
				$this->ffm->delete($d["id"]);
			}
		}
	}

	function all_clear(Controller $ctl) {

		$ctl->show_multi_dialog("DeleteAll", "all_clear.tpl", "Edit Translation");
	}

	function all_clear_exe(Controller $ctl) {
		$this->ffm->allclear();

		$ctl->close_multi_dialog("DeleteAll");

		$ctl->ajax("lang", "showlist");
	}

	function blank_clear(Controller $ctl) {

		$list = $this->ffm->getall("en", SORT_ASC);

		foreach ($list as $d) {
			if (empty($d["jp"])) {
				$this->ffm->delete($d["id"]);
			}
		}

		$ctl->ajax("lang", "edit");
	}

	function open_edit_dialog(Controller $ctl) {
		$post = $ctl->POST();
		$data = json_decode((string) ($post["data"] ?? ""), true);

		$arr = [];
		foreach ($data as $d) {
			$classname = $d["classname"];
			$en = $d["en"];

			$list = $this->ffm->select(["classname", "en"], [$classname, $en]);
			if (count($list) > 0) {
				$d["jp"] = $list[0]["jp"];
			}
			$arr[$classname . $en] = $d;
		}

		$ctl->assign("arr", $arr);
		$ctl->show_multi_dialog("lang", "edit.tpl", "Translate", 800);
	}

	// Ajaxで取得する関数
	function list(Controller $ctl) {

		$list = $this->ffm->getall("en", SORT_ASC);

		$newlist = array();
		foreach ($list as $d) {
			$newlist[$d["classname"]][$d["en"]] = $d;
		}

		// Default
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

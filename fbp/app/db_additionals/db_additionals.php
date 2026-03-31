<?php


class db_additionals {
	
	private $place_opt = [0 => "Top Section", 1 => "Each Row", 2=>"Bottom Section of side table", 3=>"Each Row of side table"];
	private $button_type_opt = [0=>"Text",1=>"Icon(Google Material Icons)"];
	private $show_button_opt = [0=>"Show",1=>"Hide"];
	private $window="db_additionals";


	function __construct(Controller $ctl) {
		$ctl->assign("place_opt", $this->place_opt);
		$ctl->assign("button_type_opt",$this->button_type_opt);
		$ctl->assign("show_button_opt",$this->show_button_opt);
	}

	function list(Controller $ctl) {

		$items = $ctl->db("additionals")->getall("id",SORT_DESC);
		
		foreach($items as $key=>$item){
			if($item["class_name"] == "admin"){
				unset($items[$key]);
				continue;
			}
		}

		$ctl->assign("items", $items);

		$ctl->reload_area("#tabs-buttons", "list.tpl");
	}

	function add(Controller $ctl) {
		
		$post = $ctl->POST();
		$post["function_name"] = "run";
		$post["button_type"] = 0;
		
		$db_id = $post["id"];
		
		if(!empty($db_id)){
			$db = $ctl->db("db","db")->get($db_id);
			$post["tb_name"] = $db["tb_name"];
			$ctl->assign("reflesh_db",$db_id);
		}
		
		$database_list = $ctl->db("db", "db")->getall("sort",SORT_ASC);
		foreach ($database_list as $d) {
			$database_names[$d["tb_name"]] = $d["tb_name"];
		}
		$ctl->assign("database_names", $database_names);
		$ctl->assign("post",$post);


		$ctl->show_multi_dialog($this->window . "edit", "add.tpl", $ctl->t("db_additionals.dialog.add"), 1000);
	}

	function add_exe(Controller $ctl) {

		$post = $ctl->POST();
		$class_name = trim((string)($post["class_name"] ?? ""));
		$function_name = trim((string)($post["function_name"] ?? ""));

		if (!preg_match('/^[A-Za-z][A-Za-z0-9_-]*$/', $class_name)) {
			$ctl->res_error_message("class_name", $ctl->t("db_additionals.validation.class_name_format"));
		}
		if (!preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $function_name)) {
			$ctl->res_error_message("function_name", $ctl->t("db_additionals.validation.function_name_format"));
		}
		if (empty($post["button_title"])) {
			$ctl->res_error_message("button_title", $ctl->t("validation.required"));
		}
		if (empty($post["tb_name"])) {
			$ctl->res_error_message("place", $ctl->t("validation.required"));
		}

		$exists = $ctl->db("additionals")->select("class_name", $class_name);
		if (count($exists) > 0) {
			$ctl->res_error_message("class_name", $ctl->t("db_additionals.validation.class_name_exists"));
		}
		


		if ($ctl->count_res_error_message() == 0) {
			$save = [];
			$save["tb_name"] = trim((string)$post["tb_name"]);
			$save["button_title"] = trim((string)$post["button_title"]);
			$save["class_name"] = $class_name;
			$save["function_name"] = $function_name;
			$save["place"] = (int)($post["place"] ?? 0);
			$save["button_type"] = (int)($post["button_type"] ?? 0);
			$save["code_type"] = [];
			$save["reload"] = 0;
			$save["user_request"] = "";
			$save["databases"] = [];
			$save["close_button"] = 2;
			$save["ui_mode"] = 0;
			$save["show_button"] = 0;
			$save["dialog_width"] = 800;

			if($save["button_type"]==1){
				$save["button_title"] = strtolower($save["button_title"]);
				$save["button_title"] = str_replace(" ", "_", $save["button_title"]);
			}

			$ctl->db("additionals")->insert($save);
			$save["sort"] = (int)($save["id"] ?? 0);
			$ctl->db("additionals")->update($save);

			$ctl->reload_work_area();
			$ctl->reload_side_panel();
			$ctl->invoke("edit",["id"=>$save["id"]]);
			$ctl->close_multi_dialog($this->window . "edit");
		} else {
			//$ctl->show_notification_text("There are errors. Please correct them and try again.", 2, "#950000", "#FFF");
		}
	}

	function edit(Controller $ctl) {
		$post = $ctl->POST();
		$id = $post["id"];
		
		$ctl->assign("reload_db_id",$post["reload_db_id"]);

		$row = $ctl->db("additionals")->get($id);
		
		$ctl->assign("post", $row);

		$database_list = $ctl->db("db", "db")->getall("sort",SORT_ASC);
		foreach ($database_list as $d) {
			$database_names[$d["tb_name"]] = $d["tb_name"];
		}
		$ctl->assign("database_names", $database_names);

		$ctl->show_multi_dialog($this->window . "edit", "edit.tpl", $ctl->t("db_additionals.dialog.edit"), 1000);
	}

	function edit_exe(Controller $ctl) {

		$post = $ctl->POST();
		$current = $ctl->db("additionals")->get((int)$post["id"]);
		$class_name = trim((string)($post["class_name"] ?? ""));
		$function_name = trim((string)($post["function_name"] ?? ""));

		if (!preg_match('/^[A-Za-z][A-Za-z0-9_-]*$/', $class_name)) {
			$ctl->res_error_message("class_name", $ctl->t("db_additionals.validation.class_name_format"));
		}
		if (!preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $function_name)) {
			$ctl->res_error_message("function_name", $ctl->t("db_additionals.validation.function_name_format"));
		}
		if (empty($post["button_title"])) {
			$ctl->res_error_message("button_title", $ctl->t("validation.required"));
		}
		if (empty($post["tb_name"])) {
			$ctl->res_error_message("place", $ctl->t("validation.required"));
		}

		if ($ctl->count_res_error_message() == 0) {

			$save = $current;
			$save["tb_name"] = trim((string)$post["tb_name"]);
			$save["button_title"] = trim((string)$post["button_title"]);
			$save["class_name"] = $class_name;
				$save["function_name"] = $function_name;
				$save["place"] = (int)($post["place"] ?? 0);
				$save["button_type"] = (int)($post["button_type"] ?? 0);
				$save["show_button"] = (int)($post["show_button"] ?? 0);
				if($save["button_type"]==1){
					$save["button_title"] = strtolower($save["button_title"]);
					$save["button_title"] = str_replace(" ", "_", $save["button_title"]);
				}
			$ctl->db("additionals")->update($save);

			if($post["close"] != "false"){
				$ctl->close_multi_dialog($this->window . "edit");
			}else{
				$ctl->invoke("edit",["id"=>$post["id"]]);
			}
			
			$ctl->reload_work_area();
			$ctl->reload_side_panel();
			$ctl->close_multi_dialog($this->window . "edit");
		} else {
			$ctl->show_notification_text($ctl->t("db_additionals.validation.fix_errors"), 2, "#950000", "#FFF");
		}
	}

	function delete(Controller $ctl){
		$post = $ctl->POST();
		$id = $post["id"];
		
		$ctl->assign("reload_db_id",$post["reload_db_id"]);
		
		$data = $ctl->db("additionals")->get($id);
		$ctl->assign("data",$data);
		
		$ctl->close_multi_dialog("edit");
		
		$ctl->show_multi_dialog($this->window . "delete", "delete.tpl", $ctl->t("db_additionals.dialog.delete"));
	}
	
	function delete_exe(Controller $ctl){
		$post = $ctl->POST();
		$id = $post["id"];
		
		$data = $ctl->db("additionals")->get($id);
		$ctl->db("additionals")->delete($id);

		$setting = $ctl->get_setting();
		$source_code_dir = trim((string)($setting["source_code_dir"] ?? ""));
		$class_name = trim((string)($data["class_name"] ?? ""));
		if ($source_code_dir !== "" && $class_name !== "") {
			$target_dir = rtrim($source_code_dir, "/") . "/classes/app/" . $class_name;
			if (is_dir($target_dir)) {
				$this->delete_dir_contents($target_dir);
				rmdir($target_dir);
			}
		}
		
		$ctl->close_multi_dialog($this->window . "delete");
		$ctl->close_multi_dialog($this->window . "edit");
		
		if($post["reload_db_id"] != ""){
			if($data["place"]==2 || $data["place"] ==3){
				$ctl->reload_side_panel();
			}else{
				$ctl->reload_work_area();			
			}
		}else{
			$ctl->invoke("list");				
		}
	}

	private function delete_dir_contents(string $dir): bool {
		if (!is_dir($dir)) {
			return false;
		}
		if ($dir === "/" || $dir === "") {
			throw new Exception("Refusing to delete root directory.");
		}
		if (strpos($dir, "/classes/app/") === false) {
			throw new Exception("Refusing to delete outside classes/app.");
		}

		$items = scandir($dir);
		if ($items === false) {
			return false;
		}
		foreach ($items as $item) {
			if ($item === "." || $item === "..") {
				continue;
			}
			$path = $dir . DIRECTORY_SEPARATOR . $item;
			if (is_dir($path)) {
				$this->delete_dir_contents($path);
				rmdir($path);
			} else {
				unlink($path);
			}
		}
		return true;
	}

	function button_sort(Controller $ctl){
		$post = $ctl->POST();
		$tb_name = $post["tb_name"];
		$place = $post["place"];
		
		$list = $ctl->db("additionals")->select(["tb_name","place"],[$tb_name,$place],true,"AND","sort",SORT_ASC);
		$ctl->assign("additionals",$list);
		$ctl->assign("place",$place);
		$ctl->show_multi_dialog("button_sort", "sort.tpl", $ctl->t("db_additionals.dialog.sort"));
	}
	
	function button_sort_exe(Controller $ctl){
		$post = $ctl->POST();
		
		$log = $post["log"];
		$ex = explode(",",$log);
		$s = 0;
		foreach($ex as $id){
			$a = $ctl->db("additionals")->get($id);
			$a["sort"] = $s;
			$ctl->db("additionals")->update($a);
			$s++;
		}
		
		if($post["place"] == 0 || $post["place"] == 1){
			$ctl->reload_work_area();
		}else{
			$ctl->reload_side_panel();
		}
	}
}

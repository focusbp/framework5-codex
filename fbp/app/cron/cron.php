<?php


class cron {
	
	private $ffm_cron;

	public function __construct(Controller $ctl) {
		
		if($ctl->GET("function") == "exec"){	
			$ctl->set_check_login(false);
		}
		
		$this->ffm_cron = $ctl->db("cron");
		
		
		$min = [];
		for ($i = 0; $i <= 55; $i += 10) {
		    $min[(string) $i] = sprintf("%02d", $i);
		}
		$ctl->assign("min_opt",$min);

		$hour = [];
		for ($i = 0; $i <= 23; $i++) {
			$hour[(string) $i] = sprintf("%02d", $i);
		}
		$ctl->assign("hour_opt",$hour);

		$day = [];
		for ($i = 1; $i <= 31; $i++) {
			$day[(string) $i] = (string) $i;
		}
		$ctl->assign("day_opt",$day);

		$month = [];
		for ($i = 1; $i <= 12; $i++) {
			$month[(string) $i] = (string) $i;
		}
		$ctl->assign("month_opt",$month);

		$weekday = [
		    "0" => $ctl->t("cron.weekday.sun"),
		    "1" => $ctl->t("cron.weekday.mon"),
		    "2" => $ctl->t("cron.weekday.tue"),
		    "3" => $ctl->t("cron.weekday.wed"),
		    "4" => $ctl->t("cron.weekday.thu"),
		    "5" => $ctl->t("cron.weekday.fri"),
		    "6" => $ctl->t("cron.weekday.sat"),
		];
		$ctl->assign("weekday_opt",$weekday);
	}
	
	function exec(Controller $ctl){
		
		// Check encrypted code
		$id_enc = $ctl->GET("id");
		if(empty($id_enc)){
			$id_enc = $ctl->POST("id");
		}
		$id = $ctl->decrypt($id_enc);
		if(empty($id)){
			return;
		}
		
		// Allow the script to continue running even if the user closes the browser
		ignore_user_abort(true);

		// Disable the time limit (script can run indefinitely)
		set_time_limit(0);
		
		$d = $this->ffm_cron->get($id);
		
		$class_name = $d["class_name"];
		$function_name = $d["function_name"];

		$ctl->set_class($class_name);

		$obj = getClassObject($ctl, $class_name, new Dirs());

		// Execute
		try{
			$obj->$function_name($ctl);
			$d["last_log"] = date("Y/m/d H:i") . " Success!";
		}catch(Exception $e){
			$d["last_log"] = $e->getTraceAsString();
		}
		$this->ffm_cron->update($d);

		
		if($ctl->POST("_call_from") == "appcon"){
			$ctl->ajax("cron","page");
		}
		
		$this->ffm_cron->close();
	}
	
	
	function page(Controller $ctl) {

		$post = $ctl->POST();
		$ctl->assign('post', $post);
		$items = $this->ffm_cron->getall("sort", SORT_ASC);
		foreach($items as &$item){
			$item["_id_enc"] = $ctl->encrypt($item["id"]);
		}
		$ctl->assign("items", $items);
		
		// プロトコル（HTTPかHTTPSか）
		$code = $ctl->encrypt("true");
		$url = $ctl->get_APP_URL() . 'fbp/app.php?class=cron&function=exec&code=' . $code;
		$ctl->assign("url",$url);
		$ctl->assign("code",$code);

		//$ctl->show_multi_dialog("cron", "index.tpl", "Cron Tasks",1200);
		$ctl->reload_area("#tabs-cron","index.tpl");
	}
	
	function add(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);

		$ctl->show_multi_dialog("add_cron", "add.tpl", $ctl->t("cron.dialog.add"), 600, true, true);
	}
	
	function add_exe(Controller $ctl) {
		$post = $ctl->POST();
		$ctl->assign('post', $post);

		$id = $this->ffm_cron->insert($post);
		
		// cronを更新
		$ctl->cron_set();
		
		$ctl->close_multi_dialog("add_cron");

		$ctl->ajax("cron","page");
	}
	
	//view edit page
	function edit(Controller $ctl) {
		$post = $ctl->POST();
		$id = $post["id"];

		$data = $this->ffm_cron->get($id);		
		$ctl->assign("data", $data);
		
		$ctl->show_multi_dialog("edit_cron", "edit.tpl", $ctl->t("cron.dialog.edit"), 1000, "_edit_button.tpl", true);
	}
	
	//save edited data
	function edit_exe(Controller $ctl) {
		$post = $ctl->POST();
		
		$post["min"] = $post["min"] ?? [];
		$post["hour"] = $post["hour"] ?? [];
		$post["day"] = $post["day"] ?? [];
		$post["month"] = $post["month"] ?? [];
		$post["weekday"] = $post["weekday"] ?? [];
		
		$d = $this->ffm_cron->get($post["id"]);
		foreach($post as $key=>$val){
			$d[$key] = $post[$key];
		}

		$this->ffm_cron->update($d);
		
		// cronを更新
		$ctl->cron_set();

		$ctl->close_multi_dialog("edit_cron");
		$ctl->ajax("cron","page");
	}
	
	function delete(Controller $ctl) {
		$id = $ctl->POST("id");
		$data = $this->ffm_cron->get($id);
		$ctl->assign("data", $data);
		$ctl->show_multi_dialog("delete_additional", "delete.tpl", $ctl->t("cron.dialog.delete"), 500, true, true);
	}

	//delete data form database
	function delete_exe(Controller $ctl) {
		$id = $ctl->POST("id");

		$this->ffm_cron->delete($id);
		
		// cronを更新
		$ctl->cron_set();
		
		$ctl->close_multi_dialog("delete_additional");
		$ctl->ajax("cron","page");
	}

	function sort(Controller $ctl) {
		$post = $ctl->POST();
		$logArr = explode(',', $post['log']);
		$c = 1;
		foreach ($logArr as $id) {
			$d = $this->ffm_cron->get($id);
			$d['sort'] = $c;
			$this->ffm_cron->update($d);
			$c++;
		}
	}

}

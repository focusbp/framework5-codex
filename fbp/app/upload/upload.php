<?php

class upload {

	private $ffm;

	function __construct(Controller $ctl) {

		if ($ctl->GET("function") == "pull"
			|| $ctl->GET("function") == "favicon") {
			$ctl->set_check_login(false);
			return;
		}

		$this->ffm = $ctl->db("sliced_file");
	}

	function change_to_vimeo_id(Controller $ctl) {
		
		$txt = $_POST["txt"] ?? "";
		$vimeo = $ctl->create_vimeo();
		$vimeo_id = $vimeo->get_vimeo_id_from_URL($txt);
		if ($vimeo_id === false) {
			$vimeo_id = "";
		}
		$ctl->append_res_data("vimeo_id", $vimeo_id);
	}

	function sliced_data(Controller $ctl) {

		$uploadDir = $ctl->dirs->datadir . "/upload/";

		// Retrieve POST data
		$partData = base64_decode((string) ($_POST["slicedata"] ?? ""));
		$filename = $_POST["filename"] ?? "";
		$partIndex = (int) ($_POST["k"] ?? 0);
		$totalParts = (int) ($_POST["totalParts"] ?? 0);
		$sliced_file_id = $_POST["sliced_file_id"] ?? "";
		$d = [];

		// Create the file if the first part is being uploaded
		if ($partIndex == 1) {
			// Ensure upload directory exists
			if (!is_dir($uploadDir)) {
				mkdir($uploadDir);
			}

			$d["time_created"] = time();
			$d["filename"] = $filename;
			$this->ffm->insert($d);
			$sliced_file_id = $d["id"];
			$pathname = "sliced_" . $sliced_file_id;
			$d["pathname"] = $pathname;
			$this->ffm->update($d);

			// Open the file for writing
			$file = fopen($uploadDir . $pathname, 'w');
		} else {

			$d = $this->ffm->get($sliced_file_id);
			$pathname = $d["pathname"];

			// Append to the existing file
			$file = fopen($uploadDir . $pathname, 'a');
		}

		// Write the current part data
		fwrite($file, $partData);
		fclose($file);

		// If this is the last part, finalize the upload
		$ret = [];
		$ret["sliced_file_id"] = $sliced_file_id;

		if ($partIndex == $totalParts) {
			$d["flg_finish"] = 1;
			$this->ffm->update($d);
		}

		$ctl->append_res_data("sliced", $ret);
	}

	function send_to_vimeo(Controller $ctl) {
		$sliced_file_id = $ctl->POST("sliced_file_id");
		$title = $ctl->POST("title");
		$description = $ctl->POST("description");

		$d = $this->ffm->get($sliced_file_id);
		$pathname = $d["pathname"];

		$uploadDir = $ctl->dirs->datadir . "/upload/";
		$file_size = filesize($uploadDir . $pathname);

		$download_url = $ctl->get_APP_URL("upload", "pull") . "&path=" . $ctl->encrypt($pathname);

		$vimeo = $ctl->create_vimeo();
		$vimeo_id = $vimeo->upload($download_url, $file_size);

		if ($vimeo_id != null) {

			$vimeo->edit($vimeo_id, $title, $description);

			$res["result"] = "success";
			$res["vimeo_id"] = $vimeo_id;
		} else {
			$res["result"] = "fail";
		}
		$ctl->append_res_data("vimeo", $res);
	}

	function pull(Controller $ctl) {
		$path = $ctl->GET("path");
		$pathname = $ctl->decrypt($path);

		$uploadDir = $ctl->dirs->datadir . "/upload/";

		$file_path = $uploadDir . $pathname;

		// ファイルが存在するかチェック
		if (file_exists($file_path)) {
			// ファイルのタイプを取得
			$mime_type = mime_content_type($file_path);
			$file_name = basename($file_path);

			// HTTPヘッダーを設定
			header('Content-Type: ' . $mime_type);
			header('Content-Disposition: inline; filename="' . $file_name . '"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file_path));

			// 出力バッファを無効にする
			if (ob_get_level()) {
				ob_end_clean();
			}

			// ファイルをチャンクごとに読み込んで出力する
			$chunk_size = 1024 * 1024; // 1MBずつ読み込み
			$file = fopen($file_path, 'rb');
			if ($file !== false) {
				while (!feof($file)) {
					echo fread($file, $chunk_size);
					// バッファリングを強制的にフラッシュする
					flush();
				}
				fclose($file);
			}
			exit;
		} else {
			header('HTTP/1.1 404 Not Found');
			echo "File does not exist";
		}
	}
	
	function favicon(Controller $ctl) {
		if($ctl->is_saved_file("favicon")){
			$ctl->res_saved_image("favicon");
		}else{
			$ctl->res_redirect("app.php?class=base&function=img&file=favicon.ico");
		}
	}
}

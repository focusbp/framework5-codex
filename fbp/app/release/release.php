<?php

require_once dirname(__FILE__) . "/ReleaseManager.php";

class release {

	private $release_manager;

	function __construct(Controller $ctl) {
		$this->release_manager = new ReleaseManager();
	}

	function download_zip(Controller $ctl) {

		$setting = $ctl->get_setting();
		$ctl->assign("setting", $setting);
		if (empty($setting["project_release_code"])) {
			$ctl->assign("message", $ctl->t("release.validation.project_release_code_required"));
			$ctl->assign("flg", false);
		} else {
			$ctl->assign("message", $ctl->t("release.download_project_message", ["code" => $setting["project_release_code"]]));
			$ctl->assign("flg", true);
		}

		$ctl->show_multi_dialog("download", "download.tpl", $ctl->t("release.dialog.download_package"));
	}

	function download_zip_exe(Controller $ctl) {
		$zipFile = $this->release_manager->create_release_zip($ctl);
		if (!is_file($zipFile)) {
			throw new Exception("Release zip was not created.");
		}

		while (ob_get_level() > 0) {
			ob_end_clean();
		}

		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=\"" . basename($zipFile) . "\"");
		header("Content-Length: " . filesize($zipFile));
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Pragma: public");

		readfile($zipFile);

		unlink($zipFile);
		exit;
	}

	function release(Controller $ctl) {
		$setting = $ctl->get_setting();
		$ctl->assign("setting", $setting);
		if (empty($setting["project_release_code"])) {
			$ctl->assign("message", $ctl->t("release.validation.project_release_code_required"));
			$ctl->assign("flg", false);
		} else {
			$ctl->assign("message", $ctl->t("release.release_project_message", ["code" => $setting["project_release_code"]]));
			$ctl->assign("flg", true);
		}
		$ctl->show_multi_dialog("upgrade", "release.tpl", $ctl->t("release.dialog.release"), 600, true, true);
	}

	function release_confirm(Controller $ctl) {

		$setting = $ctl->get_setting();
		$saved_release_file = "release.zip";

		if ($ctl->is_saved_file($saved_release_file)) {
			$ctl->remove_saved_file($saved_release_file);
		}

		// Upload file to server 
		$ctl->save_posted_file('release_file', $saved_release_file);
		$zipFile = $ctl->get_saved_filepath($saved_release_file);

		try {
			$info = $this->release_manager->validate_release_zip($ctl, $zipFile);
			$ctl->assign("info", $info);
			$ctl->assign("flg", true);
		} catch (Throwable $e) {
			$ctl->assign("message", $e->getMessage());
			$ctl->assign("flg", false);
			if (is_file($zipFile)) {
				unlink($zipFile);
			}
		}
		$ctl->show_multi_dialog("upgrade", "release_confirm.tpl", $ctl->t("release.dialog.upgrade"), 600, true, true);
	}

	function release_exe(Controller $ctl) {

		$saved_release_file = "release.zip";
		$zipFile = $ctl->get_saved_filepath($saved_release_file);

		try {
			$this->release_manager->apply_release_zip($ctl, $zipFile);
			$ctl->assign("success", $ctl->t("release.success"));
		} catch (Throwable $e) {
			$ctl->assign("fail", $e->getMessage());
		}
		
		$ctl->show_multi_dialog("upgrade", "release_done.tpl", $ctl->t("release.dialog.upgrade"), 600, true, true);
	}

	function reload(Controller $ctl) {
		$ctl->res_reload();
	}
}

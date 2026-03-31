<?php

class codex_beginner {
	public function run(Controller $ctl) {
		$ctl->show_multi_dialog("codex_beginner", "beginner.tpl", $ctl->t("codex_beginner.dialog.title"), 980);
	}

	public function close_dialog(Controller $ctl) {
		$ctl->close_multi_dialog("codex_beginner");
	}
}

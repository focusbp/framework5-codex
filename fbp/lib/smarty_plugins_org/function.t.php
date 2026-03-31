<?php

function smarty_function_t($params, Smarty_Internal_Template $template) {
	$key = trim((string) ($params["key"] ?? ""));
	if ($key === "") {
		return "";
	}

	$lang = isset($params["lang"]) ? (string) $params["lang"] : null;
	$assign = isset($params["assign"]) ? (string) $params["assign"] : "";

	unset($params["key"], $params["lang"], $params["assign"]);

	$ctl = $template->getTemplateVars("_ctl");
	if (!is_object($ctl)) {
		$ctl = $template->getTemplateVars("ctl");
	}

	$text = $key;
	if (is_object($ctl) && method_exists($ctl, "t")) {
		$text = $ctl->t($key, is_array($params) ? $params : [], $lang);
	}

	if ($assign !== "") {
		$template->assign($assign, $text);
		return "";
	}

	return $text;
}

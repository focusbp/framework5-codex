<?php

if (!function_exists('smarty_function_escape_special_chars')) {
	require_once __DIR__ . "/../../lib_ext/smarty-5.8.0/src/functions.php";
}

function smarty_function_html_year_month($params, Smarty_Internal_Template $template)
{
    $value = (string) ($params['value'] ?? '');
    if ($value === '') {
        return '';
    }

    $setting = $template->getTemplateVars("setting");
    $year_month_format = !empty($setting["year_month_format"]) ? (string) $setting["year_month_format"] : "Y/m";

    $normalized = preg_replace('/[^0-9]/', '', $value);
    if (strlen($normalized) < 6) {
        return $value;
    }

    $year = substr($normalized, 0, 4);
    $month = substr($normalized, 4, 2);

    return strtr($year_month_format, [
        'Y' => $year,
        'm' => $month,
        'n' => (string) intval($month, 10),
    ]);
}

<?php

function fbp_include_smarty(): void {
	include_once __DIR__ . "/../lib_ext/smarty-5.8.0/libs/Smarty.class.php";
	include_once __DIR__ . "/../lib_ext/smarty-5.8.0/src/functions.php";
	if (!defined('SMARTY_PLUGINS_DIR')) {
		define('SMARTY_PLUGINS_DIR', __DIR__ . "/../lib_ext/smarty-5.8.0/libs/plugins/");
	}
	if (class_exists('\Smarty\Smarty') && !class_exists('Smarty')) {
		class_alias('\Smarty\Smarty', 'Smarty');
	}
	if (class_exists('\Smarty\Template') && !class_exists('Smarty_Internal_Template')) {
		class_alias('\Smarty\Template', 'Smarty_Internal_Template');
	}
}

function fbp_register_smarty_plugins($smarty, string $pluginDir): void {
	$types = [
		'function',
		'modifier',
		'block',
		'compiler',
		'prefilter',
		'postfilter',
		'outputfilter',
		'modifiercompiler',
	];

	$normalizedDir = rtrim($pluginDir, '/\\') . DIRECTORY_SEPARATOR;

	foreach ($types as $type) {
		foreach (glob($normalizedDir . $type . '.?*.php') as $filename) {
			$pluginName = fbp_get_smarty_plugin_name_from_filename($filename);
			if ($pluginName === null) {
				continue;
			}
			require_once $filename;
			$callback = 'smarty_' . $type . '_' . $pluginName;
			if (function_exists($callback) || class_exists($callback)) {
				$smarty->registerPlugin($type, $pluginName, $callback, true, []);
			}
		}
	}

	foreach (glob($normalizedDir . 'resource.?*.php') as $filename) {
		$pluginName = fbp_get_smarty_plugin_name_from_filename($filename);
		if ($pluginName === null) {
			continue;
		}
		require_once $filename;
		$className = 'smarty_resource_' . $pluginName;
		if (class_exists($className)) {
			$smarty->registerResource($pluginName, new $className());
		}
	}

	foreach (glob($normalizedDir . 'cacheresource.?*.php') as $filename) {
		$pluginName = fbp_get_smarty_plugin_name_from_filename($filename);
		if ($pluginName === null) {
			continue;
		}
		require_once $filename;
		$className = 'smarty_cacheresource_' . $pluginName;
		if (class_exists($className)) {
			$smarty->registerCacheResource($pluginName, new $className());
		}
	}

	fbp_register_php_compat_modifiers($smarty);
}

function fbp_get_smarty_plugin_name_from_filename(string $filename): ?string {
	if (!preg_match('/.*\.([a-zA-Z0-9_]+)\.php$/', $filename, $matches)) {
		return null;
	}
	return $matches[1];
}

function fbp_register_php_compat_modifiers($smarty): void {
	$modifiers = [
		'date' => 'fbp_smarty_phpfunc_date',
		'number_format' => 'fbp_smarty_phpfunc_number_format',
		'trim' => 'fbp_smarty_phpfunc_trim',
		'preg_replace' => 'fbp_smarty_phpfunc_preg_replace',
		'floor' => 'fbp_smarty_phpfunc_floor',
		'json_encode' => 'fbp_smarty_phpfunc_json_encode',
		'time' => 'fbp_smarty_phpfunc_time',
		'mb_strimwidth' => 'fbp_smarty_phpfunc_mb_strimwidth',
		'str_replace' => 'fbp_smarty_phpfunc_str_replace',
		'ucfirst' => 'fbp_smarty_phpfunc_ucfirst',
		'implode' => 'fbp_smarty_phpfunc_implode',
		'serialize' => 'fbp_smarty_phpfunc_serialize',
		'strtotime' => 'fbp_smarty_phpfunc_strtotime',
		'count' => 'fbp_smarty_phpfunc_count',
	];

	foreach ($modifiers as $name => $callback) {
		$smarty->registerPlugin('modifier', $name, $callback, true, []);
	}
}

function fbp_smarty_phpfunc_date($format, $value = null) {
	$format = (string) $format;
	if ($format === "") {
		$format = "Y-m-d H:i:s";
	}
	if ($value === null || $value === "") {
		return date($format);
	}
	if ($value instanceof DateTimeInterface) {
		return $value->format($format);
	}
	if (is_numeric($value)) {
		return date($format, (int) $value);
	}
	$timestamp = strtotime((string) $value);
	if ($timestamp === false) {
		return "";
	}
	return date($format, $timestamp);
}

function fbp_smarty_phpfunc_number_format($value, $decimals = 0, $decimal_separator = ".", $thousands_separator = ",") {
	return number_format((float) $value, (int) $decimals, (string) $decimal_separator, (string) $thousands_separator);
}

function fbp_smarty_phpfunc_trim($value, $characters = " \n\r\t\v\0") {
	return trim((string) $value, (string) $characters);
}

function fbp_smarty_phpfunc_preg_replace($pattern, $replacement, $subject = "", $limit = -1) {
	$result = preg_replace((string) $pattern, (string) $replacement, (string) $subject, (int) $limit);
	return $result === null ? "" : $result;
}

function fbp_smarty_phpfunc_floor($value) {
	return floor((float) $value);
}

function fbp_smarty_phpfunc_json_encode($value, $flags = 0, $depth = 512) {
	$result = json_encode($value, (int) $flags, (int) $depth);
	return $result === false ? "" : $result;
}

function fbp_smarty_phpfunc_time($_unused = null) {
	return time();
}

function fbp_smarty_phpfunc_mb_strimwidth($value, $start = 0, $width = 0, $trimmarker = "") {
	return mb_strimwidth((string) $value, (int) $start, (int) $width, (string) $trimmarker);
}

function fbp_smarty_phpfunc_str_replace($search, $replace, $subject = "") {
	return str_replace($search, $replace, (string) $subject);
}

function fbp_smarty_phpfunc_ucfirst($value) {
	return ucfirst((string) $value);
}

function fbp_smarty_phpfunc_implode($separator, $array = null) {
	if ($array === null && is_array($separator)) {
		return implode("", $separator);
	}
	if (!is_array($array)) {
		return "";
	}
	return implode((string) $separator, $array);
}

function fbp_smarty_phpfunc_serialize($value) {
	return serialize($value);
}

function fbp_smarty_phpfunc_strtotime($value, $baseTimestamp = null) {
	if ($value === null || $value === "") {
		return "";
	}
	$timestamp = $baseTimestamp === null ? strtotime((string) $value) : strtotime((string) $value, (int) $baseTimestamp);
	return $timestamp === false ? "" : $timestamp;
}

function fbp_smarty_phpfunc_count($value, $mode = COUNT_NORMAL) {
	if (is_array($value) || $value instanceof Countable) {
		return count($value, (int) $mode);
	}
	if ($value === null || $value === "") {
		return 0;
	}
	return 1;
}

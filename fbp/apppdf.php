<?php

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
mb_language("Japanese");
mb_internal_encoding("UTF-8");

//キャッシュを防ぐためにURLを変更してリダイレクト
if (empty($_GET["time"])) {
	header("Location: apppdf.php?time=" . strtotime("now"));
	return;
}

if (isset($_REQUEST[session_name()])) {
    session_id($_REQUEST[session_name()]);
}
session_start();

include("lib/pdfmaker/function_image.php");
include("lib/pdfmaker/pdfmaker_class.php");
include("lib/pdfmaker/macFileNameNormalizer.php");
$mac_normalizer = new macFileNameNormalizer();

$txt = $_SESSION["pdf_text"];
$pdf_filename = $_SESSION["pdf_filename"];
if (!endsWith($pdf_filename, ".pdf")) {
	$pdf_filename .= ".pdf";
}
$imgdir = $_SESSION["pdf_imgdir"];

// 文字化け対応
$txt = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $txt);
$txt = $mac_normalizer->normalizeUtf8MacFileName($txt); //Macの濁点問題
$pdf_filename = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $pdf_filename);

// Kangxi Radicalsの変換
//$krl = file_get_contents("kangxi_radicals_list.txt");
//$ex = explode("\n", $krl);
//$search = array();
//$replace = array();
//foreach($ex as $line){
//	$s1 = mb_substr($line,0,1);
//	$s2 = mb_substr($line,1,1);
//	if(!empty($s2)){
//		$search[] = $s1;
//		$replace[] = $s2;
//	}
//}
//$txt = str_replace($search,$replace,$txt);

$pdfmaker = new pdfmaker_class();

// ------------------------------
// デバッグ　文字コードを表示
// ------------------------------
//for($i=0;$i< mb_strlen($txt);$i++){
//	$t = mb_substr($txt,$i, 1);
//	echo $t . ":" . bin2hex($t) . "<br>";
//}

if (($_GET["cmd"] ?? "") == "download") {
	if (is_smartphone() && (!is_line_inapp_browser($ua))) {
		$pdfmaker->makepdf($txt, $imgdir, $pdf_filename, "D");
	} else {
		$pdfmaker->makepdf($txt, $imgdir, $pdf_filename);
	}
	
} else {
	if (is_smartphone()) {
		$sid = SID;
		$url = 'apppdf.php?cmd=download&time=' . time();
		$url = url_with_sid($url);
		$html = file_get_contents(dirname(__FILE__) . "/lib/pdfmaker/downloadpage.tpl");
		$html = str_replace('{$url}', $url, $html);
		echo $html;
	} else {
		$pdfmaker->makepdf($txt, $imgdir, $pdf_filename);
	}
}

function url_with_sid(string $url): string {
	$sidParam = session_name() . '=' . rawurlencode(session_id());
    $hasQuery = (parse_url($url, PHP_URL_QUERY) !== null);
    return $url . ($hasQuery ? '&' : '?') . $sidParam;
}

//----------------------------------------
// スマホ判定
//----------------------------------------

function is_line_inapp_browser(string $ua=null): bool {
	if (is_null($ua)) {
		$ua = $_SERVER['HTTP_USER_AGENT'];
	}

	$ua_l = strtolower($ua);

	// LINE in-app browser: "Line/" を含むことが多い
	if (strpos($ua_l, ' line/') !== false){
		return true;
	}
	
	if (strpos($ua_l, 'line/') !== false){
		return true;
	}

	return false;
}

function is_smartphone($ua = null) {
	if (is_null($ua)) {
		$ua = $_SERVER['HTTP_USER_AGENT'];
	}

	if (preg_match('/iPhone|iPod|iPad|Android/ui', $ua)) {
		return true;
	} else {
		return false;
	}
}

//----------------------------------------
// 文字の後方の一致
//----------------------------------------
function endsWith($haystack, $needle) {
	$length = strlen($needle);
	if ($length == 0) {
		return true;
	}

	return (substr($haystack, -$length) === $needle);
}


//------------------------------------
// FOCUS Business Platform
// Ver 4
//------------------------------------

// 自動翻訳を停止
$(document).ready(function () {
	// <html>タグにtranslate="no"属性を追加する
	$('html').attr('translate', 'no');
});

function escapeHtml(str) {
	return String(str)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;');
}

function escapeAttributeSelectorValue(str) {
	return String(str).replace(/\\/g, "\\\\").replace(/"/g, '\\"');
}

function normalizeErrorScope(scope, dialog_id, classname) {
	if (scope !== undefined && scope !== null && scope !== "") {
		return String(scope);
	}
	if (dialog_id !== undefined && dialog_id !== null && dialog_id !== "") {
		return String(dialog_id);
	}
	if (classname !== undefined && classname !== null && classname !== "") {
		return String(classname);
	}
	return "";
}

function clearScopedErrorMessages(scope) {
	if (scope === "") {
		return false;
	}
	var escapedScope = escapeAttributeSelectorValue(scope);
	var scopedElements = $('[data-error-scope="' + escapedScope + '"] .error_message');
	if (scopedElements.length > 0) {
		scopedElements.html("");
		return true;
	}
	if (scope.charAt(0) === "#" && $(scope + " .error_message").length > 0) {
		$(scope + " .error_message").html("");
		return true;
	}
	return false;
}

function findErrorMessageTarget(scope, dialog_id, field) {
	var escapedField = escapeAttributeSelectorValue(field);
	var target = $();

	if (scope !== "") {
		var escapedScope = escapeAttributeSelectorValue(scope);
		target = $('[data-error-scope="' + escapedScope + '"] [data-error-for="' + escapedField + '"]');
		if (target.length === 0 && scope.charAt(0) === "#") {
			target = $(scope + ' [data-error-for="' + escapedField + '"]');
		}
		if (target.length === 0) {
			target = $('[data-error-scope="' + escapedScope + '"] .error_' + field);
		}
	}

	if (target.length === 0 && dialog_id !== "") {
		target = $(dialog_id + " .error_" + field);
	}
	if (target.length === 0) {
		target = $(".error_" + field);
	}
	return target;
}

function normalizeErrorMessageArray(errormessage) {
	if (Array.isArray(errormessage)) {
		return errormessage;
	}
	if (errormessage && typeof errormessage === "object") {
		return Object.values(errormessage);
	}
	return [];
}

function ensureClassStylesheet(classname) {
	if (classname === undefined || classname === null || classname === "") {
		return;
	}

	var id = "dynamic-appcss-" + classname;
	if (document.getElementById(id)) {
		return;
	}

	var appcssLinks = document.querySelectorAll('link[rel="stylesheet"][href*="appcss.php"]');
	for (var i = 0; i < appcssLinks.length; i++) {
		var href = appcssLinks[i].getAttribute("href") || "";
		if (href.indexOf("class=" + encodeURIComponent(classname)) !== -1 || href.indexOf("css_class=" + encodeURIComponent(classname)) !== -1) {
			return;
		}
	}

	var href = "appcss.php?class=" + encodeURIComponent(classname) + "&css_class=" + encodeURIComponent(classname) + "&ts=" + Date.now();
	var link = document.createElement("link");
	link.id = id;
	link.rel = "stylesheet";
	link.href = href;
	document.head.appendChild(link);
}

// embed_app iframe auto-height support (child side).
(function setupEmbedAppAutoHeight() {
	if (window.parent === window) {
		return;
	}

	var scheduled = false;
	function calcHeight() {
		var body = document.body;
		var doc = document.documentElement;
		var h1 = body ? body.scrollHeight : 0;
		var h2 = body ? body.offsetHeight : 0;
		var h3 = doc ? doc.scrollHeight : 0;
		var h4 = doc ? doc.offsetHeight : 0;
		return Math.max(h1, h2, h3, h4, 180);
	}

	function postHeightNow() {
		window.parent.postMessage({type: "embed_app:height", height: calcHeight()}, "*");
	}

	function schedulePost() {
		if (scheduled) {
			return;
		}
		scheduled = true;
		setTimeout(function () {
			scheduled = false;
			postHeightNow();
		}, 50);
	}

	window.addEventListener("message", function (event) {
		var data = event.data || {};
		if (data.type === "embed_app:request_height") {
			schedulePost();
		}
	});

	window.addEventListener("load", schedulePost);
	window.addEventListener("resize", schedulePost);
	document.addEventListener("DOMContentLoaded", schedulePost);

	if (typeof ResizeObserver !== "undefined") {
		var ro = new ResizeObserver(schedulePost);
		if (document.body) {
			ro.observe(document.body);
		}
		if (document.documentElement) {
			ro.observe(document.documentElement);
		}
	}

	if (typeof MutationObserver !== "undefined") {
		var target = document.body || document.documentElement;
		if (target) {
			var mo = new MutationObserver(schedulePost);
			mo.observe(target, {childList: true, subtree: true});
		}
	}

	setTimeout(schedulePost, 0);
	setTimeout(schedulePost, 300);
	setTimeout(schedulePost, 1200);
})();

// クッキーのパス
function cookieOpt() {
	const path = location.pathname;

	// ディレクトリ部分を取得（末尾のファイル/セグメントを落とす）
	let dir = path.replace(/\/[^/]*$/, ''); // 例: /miclub/fbp
	dir = dir.replace(/\/$/, '');           // 末尾 / を一旦除去 → /miclub/fbp or /miclub or ''

	// 最後が /fbp なら取り除いて一つ上へ
	dir = dir.replace(/\/fbp$/, '');

	// Cookie 用のパス（空や / はルートに正規化）
	const cookiePath = (dir === '' || dir === '/') ? '/' : (dir + '/');

	// 要求どおりのオプションオブジェクトを返す
	return {
		path: cookiePath,
		sameSite: 'Lax',
		secure: location.protocol === 'https:'
	};
}

//----------------------------
// 年・月 Picker
//----------------------------
var year_month_picker = false;
(function ($) {
	$.fn.year_month_picker = function () {
		$(this).wrap('<div style="position:relative;display: inline;"></div>');
		$(this).attr('autocomplete', 'off');
		var obj = this;

		// フォーカスがあたった時の処理
		this.focus(function (e) {
			$(obj).blur();

			$(".year_month_picker_panel").remove();
			$(".selectorclose").remove();

			var html = '';
			html += '<div class="year_month_picker_panel">';
			html += '<div class="picker_year"><input type="text" class="picker_year_input">&nbsp;/&nbsp;<select class="picker_month_select">';
			html += '<option value="01">1</option>';
			html += '<option value="02">2</option>';
			html += '<option value="03">3</option>';
			html += '<option value="04">4</option>';
			html += '<option value="05">5</option>';
			html += '<option value="06">6</option>';
			html += '<option value="07">7</option>';
			html += '<option value="08">8</option>';
			html += '<option value="09">9</option>';
			html += '<option value="10">10</option>';
			html += '<option value="11">11</option>';
			html += '<option value="12">12</option>';
			html += '</select>';
			html += "</div>";
			html += '<button class="picker_set lang">Set</button><button class="picker_blank lang">Clear</button>';
			html += '<div style="clear;both;"></div>'
			html += '<p class="picker_error"></p>'
			html += '</div>';
			$(this).after(html);
			var year;
			var month;
			var hiduke = new Date();
			if ($(this).val() == "") {
				year = hiduke.getFullYear();
				month = hiduke.getMonth() + 1;
			} else {
				var ex = $(this).val().split("/");
				year = ex[0];
				month = ex[1];
				if (year == undefined || isNaN(year))
					year = hiduke.getFullYear();
				if (month == undefined || isNaN(month))
					month = hiduke.getMonth() + 1;
			}
			var pad_month = ('0' + month).slice(-2);
			$(".year_month_picker_panel .picker_year_input").val(year);
			$(".year_month_picker_panel .picker_month_select").val(pad_month);
			//表示位置修正
			var target = ".year_month_picker_panel";
			var top = $(this).offset().top - $(window).scrollTop();
			var left = $(this).offset().left - $(window).scrollLeft();
			var height = $(this).outerHeight();
			var objheight = $(".year_month_picker_panel").outerHeight();
			var wh = $(window).height();
			if (top + height > wh - objheight) {
				top = wh - objheight;
			} else {
				top = top + height;
			}
			$(target).css("top", top);
			$(target).css("left", left);
			$(target).addClass('detect_outside_click');
			setTimeout(function () {
				year_month_picker = true;
			}, 200);

			var textobj = this;
			$(".year_month_picker_panel .picker_blank").click(function () {
				$(textobj).val("");
				$(".year_month_picker_panel").remove();
				$(".selectorclose").remove();
				$(obj).change();
				year_month_picker = false;
				return false;
			});
			$(".year_month_picker_panel .picker_set").click(function () {
				year = $(".year_month_picker_panel .picker_year_input").val();
				month = $(".year_month_picker_panel .picker_month_select").val();

				//年のバリデート
				var flg = true;
				if (year.length != 4) {
					flg = false;
				}
				if (year.match(/[^0-9]+/)) {
					flg = false;
				}
				if (flg) {
					$(textobj).val(year + "/" + month);
					$(obj).change();
					$(".year_month_picker_panel").remove();
					$(".selectorclose").remove();
				} else {
					$(".year_month_picker_panel .picker_error").html("Error");
				}
				year_month_picker = false;
				return false;
			});
		});
		return this;
	};
})(jQuery);

function get_formdata_with_strtotime(form) {
	$(form).find("input").each(function (index, element) {
		if ($(this).data("strtotime") == "1") {
			var val = $(this).val();
			var time = Date.parse(val) / 1000;
			if (isNaN(time)) {
				time = "";
			}
			$(this).val(time);
			$(this).attr("data-before", val);
		}
	});
	var fd = new FormData(form);

	//戻す
	$(form).find("input").each(function (index, element) {
		if ($(this).data("strtotime") == "1") {
			var val = $(this).attr("data-before");
			$(this).val(val);
		}
	});

	return fd;
}


var userAgent = window.navigator.userAgent.toLowerCase();
// ajax
$("body").on("click", ".ajax-link", function (event) {

	event.preventDefault();
	if (dialog_link_flg) {
		dialog_link_flg = false;
		setTimeout(function () {
			dialog_link_flg = true;
		}, 50);

		// formを探す
		// 1) 指定されたフォーム
		var form = $(this).data("form");
		if (form !== undefined) {
			form = $("#" + form).get(0);
		} else {
			form = null
			// 2) 親要素のフォームを探す
			var fc = $(this).closest("form");
			if (fc.length > 0) {
				form = fc.get(0);
			} else {
				// 3) .getting_dialog_id の中のフォームを探す
				var dialogParent = $(this).closest(".getting_dialog_id");
				if (dialogParent.length > 0) {
					var fdi = dialogParent.find("form");
					if (fdi.length > 0) {
						form = fdi.first().get(0); // .getting_dialog_id 内の最初のフォームを使用
					}
				}
			}
		}

		// 日付を数値ににしてFormDataを取得 (カスタムプラグインのため)
		var fd;
		if (form !== null) {
			fd = get_formdata_with_strtotime(form);
		} else {
			fd = new FormData();
		}

		// dialog_idを取得
		let tag = $(this).parents(".getting_dialog_id");
		let dialog_id = tag.attr('id');
		if (dialog_id !== undefined) {
			fd.append("_dialog_id", "#" + dialog_id);
		}

		var url = $(this).data("url");
		if (url === undefined) {
			url = "app.php";
		}
		var datalist = $(this).data();
		for (key in datalist) {
			fd.append(key, datalist[key]);
		}

		// data-_chatid を取得
		let chattag = $(this).parents(".chat-html");
		let chatid = chattag.data("_chatid");
		if (chatid !== undefined && chatid !== null && chatid !== "") {
			// chatid が "#" で始まっていなければ追加
			if (!chatid.startsWith("#")) {
				chatid = "#" + chatid;
			}

			fd.append("_chatid", chatid);

			// dialog_id が undefined の場合、_dialog_id も chatid にセット
			if (dialog_id === undefined) {
				fd.append("_dialog_id", chatid);
			}
		}

		appcon(url, fd);
	}
});

// ajax-formボタン
$("body").on("click", ".form_button", function (event) {

	var formobj = $(this).parents("form");
	var fd = new FormData(formobj.get(0));
	fd.append($(this).attr("name"), $(this).attr("value"));
	var url = formobj.attr("action");
	if (url === undefined) {
		url = "app.php";
	}
	appcon(url, fd);

	event.preventDefault();
});

// ダイアログを表示する
var dialog_link_flg = true;
function set_dialog_link() {
	$("body").on("click", ".dialog-link", function (event) {

		event.preventDefault();

		if (dialog_link_flg) {
			dialog_link_flg = false;
			setTimeout(function () {
				dialog_link_flg = true;
			}, 50);
			var fd = new FormData();
			var url = $(this).data("url");
			if (url === undefined) {
				url = "app.php";
			}
			var datalist = $(this).data();
			for (key in datalist) {
				fd.append(key, datalist[key]);
			}

			appcon(url, fd);
		}
	});
}
set_dialog_link();

/*
 * ダイアログ処理
 */
$("#dialog").dialog({
	autoOpen: false,
	width: get_dialog_width(),
	resizable: true,
	modal: true,
	show: {effect: 'fade', duration: 200},
	hide: {effect: 'fade', duration: 10},
	position: {my: "center top", at: "center top", of: window},
	buttons: [
		{
			text: "Ok",
			class: "dialog-button-ok",
			click: function () {
				var formobj = $(this).find("#dialogform");
				if (formobj != null) {
					var fd = new FormData(formobj.get(0));
					var url = formobj.attr("action");
					if (url != undefined) {
						appcon(url, fd);
					} else {
						$(this).dialog("close");
					}
				}
			}
		},
		{
			text: "Cancel",
			class: "dialog-button-cancel",
			click: function () {
				$(this).dialog("close");
			}
		}
	],
	close: function () {
		//個別に非表示にしたボタンを表示させる
		$(".ui-dialog-buttonset").show();
		$(".dialog-button-ok").show();
		$(".dialog-button-cancel").show();
		$(".ui-dialog-buttonpane").show();
	}
});

/*
 * ダイアログの横幅の自動設定
 */
function get_dialog_width(userwidth) {

	var w = window.innerWidth;
	if (w < userwidth) {
		return w * 0.9;
	}

	return userwidth;
}

/*
 * ダイアログの縦幅の自動設定
 */
function get_dialog_height() {

	var h = window.innerHeight * 0.8;
	return h;
}


/* 
 * アプリ用汎用通信関数
 */
var myChart = new Array(); //チャート用オブジェクト
var waitTimer;
var flg_reloadarea_fade = true;
function appcon(url, fd, nextfunction) {

	// 同期処理のため
	var dfd = $.Deferred();

	// chat用loading
	$("#loading").show();
	$(".class_style_ui_chat #msg").val("");
	;
	fd.append("_call_from", "appcon");

	// Timezone
	var intl_tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
	fd.append("_timezone", intl_tz);

	if (url == ".php") {
		alert("appcon: URLが不正です:" + url);
		return;
	}

	// debug_window
	var debugarr = [];
	const priorityKeys = ["class", "function"];
	priorityKeys.forEach(k => {
		if (fd.has(k)) {
			debugarr[k] = fd.get(k);
		}
	});
	fd.forEach((value, key) => {
		if (key === "class" || key === "function")
			return;
		debugarr[key] = value;
	});
	append_debug_window("POST ----> Server", debugarr, "table");

	// Fade
	var fadeflg = true;
	fadeflg = !fd.has("max");

	$("#download_view").show();
	$('#download_message').html("Sending data...");
	$('#download_progress').css({'width': 5 + '%'});

	set_windowID();

	// 送信
	$.ajax({
		async: true,
		url: url,
		type: 'POST',
		dataType: 'html',
		data: fd,
		processData: false,
		contentType: false,
		xhr: function () {
			var XHR = $.ajaxSettings.xhr();
			//進行状況表示
			if (XHR.upload) {
				XHR.upload.addEventListener('progress', function (e) {
					if (e.total > 0) {
						var load = (100 * e.loaded / e.total | 0) + 10;
						$('#download_message').html("uploading...   " + Math.round(e.loaded / 1000) + " / " + Math.round(e.total / 1000) + "kbyte");
						$('#download_progress').css({'width': load + '%'});

						if (load >= 100) {
							$('#download_message').html("Waiting for the server response...   ");
							if (waitTimer == null) {
								waitTimer = setInterval(function () {
									load += 10;
									if (load >= 110) {
										load = 0;
									}
									$('#download_progress').css({'width': load + '%'});
								}, 1000);
							}
						}
					}
				});
			}

			XHR.addEventListener('progress', function (e) {
				clearInterval(waitTimer);
				if (e.total > 0) {
					var load = (100 * e.loaded / e.total | 0) + 10;
					$('#download_message').html("downloading...   " + Math.round(e.loaded / 1000) + " / " + Math.round(e.total / 1000) + "kbyte");
					$('#download_progress').css({'width': load + '%'});
				}
			});

			return XHR;
		},
	}).done(function (data) {

		// chat用loading
		$("#loading").hide();

		clearInterval(waitTimer);
		waitTimer = null;

		$("#download_view").hide();
		$('#download_progress').css({'width': '0%'});

		if (data == "") {
			if (nextfunction) {
				nextfunction(res);
			}
			dfd.resolve();
			return;
		}

		try {
			var res;

			res = JSON.parse(data);

			if (res["error"] != null) {
				notification("", res["error"], 800, 5);
				return;
			}

			// リロード
			if (res["reload"] != null) {
				var href = location.href;
				location.assign(href);
				return;
			}

			// リダイレクト
			if (res["location"] != null) {
				location.assign(res["location"]);
				return;
			}

			if (res["close_all_dialog"] != null) {
				var exception = res["close_all_dialog"]["exception"];
				$("#multi_dialog .multi_dialog").each(function (index, element) {
					if ($(this).data("dialog_name") != exception) {
						$(this).remove();
					}
				});
			}

			if (res["close_second_work_area"] != null) {
				remove_second_work_area();
			}

			var chatform = res['chat_form'];
			for (key in chatform) {
				var htmlstr = chatform[key];
				//console.log(htmlstr);
			}

				// Error Message
				var errormessage = normalizeErrorMessageArray(res['errormessage']);
				if (res["clear_error_message"] == "true" || errormessage.length > 0) {
					var cleared = false;
					var clearScope = normalizeErrorScope(res["clear_error_scope"], "", "");
					if (clearScope !== "") {
						cleared = clearScopedErrorMessages(clearScope);
					}
					if (!cleared && errormessage.length > 0) {
						var scopedCount = 0;
						for (key in errormessage) {
							var scopeForClear = normalizeErrorScope(
									errormessage[key]["scope"],
									errormessage[key]["dialog_id"],
									errormessage[key]["classname"]
									);
							if (scopeForClear !== "" && clearScopedErrorMessages(scopeForClear)) {
								scopedCount++;
							}
						}
						cleared = scopedCount > 0;
					}
					if (!cleared) {
						$(".error_message").html("");
					}
				}
				for (key in errormessage) {
					let message = errormessage[key]["message"];
					let dialog_id = errormessage[key]["dialog_id"];
					let classname = errormessage[key]["classname"];
					let field = errormessage[key]["field"];
					let scope = normalizeErrorScope(errormessage[key]["scope"], dialog_id, classname);

					if (dialog_id == null) {
						dialog_id = "";
					}

					var target = findErrorMessageTarget(scope, dialog_id, field);
					if (target.length === 0) {
						append_debug_window("There is no tag for error_" + field + " to show the error message:" + message);
					} else {
						target.html(message).hide();
						translate();
						target.fadeIn(500, set_multidialog_height(dialog_id));
					}
				}

			var reloadarea = res['reloadarea'];
			for (key in reloadarea) {
				var htmlstr = reloadarea[key];
				if ($(key).prop("tagName") == "TEXTAREA") {
					//テキストエリアはvalueに入れる
					$(key).val(htmlstr);
				} else {
					//その他はinnerHtmlに入れる
					if ($(key).length > 0) {
						$(key).html(htmlstr);
					} else {
						append_debug_window("There is no tags for " + key);
					}
				}
				if (flg_reloadarea_fade) {
					flg_reloadarea_fade = false;
					$(key).css({opacity: '0.8'}).animate({opacity: '1'});
					setTimeout(function () {
						flg_reloadarea_fade = true;
					}, 5000);
				}


				// デフォルトのJSを動かす
				var p = $(key).parents(".getting_dialog_id");
				if (p.length > 0) {
					var dialog_id = "#" + $(p).attr("id");
					multi_dialog_functions["__all__"](dialog_id, true);
					ajax_auto_exe(dialog_id);
					if ($(dialog_id).data("ui-resizable")) {
						set_multidialog_height(dialog_id);
					}
				} else {
					multi_dialog_functions["__all__"]("body", true);
					ajax_auto_exe("body");
				}

				// クラスのJSを動かす
				var dialog_id = "#" + $(key).parents(".getting_dialog_id").attr("id");
				var classname = $(key).parents(".multi_dialog_contents").attr("data-classname");
				if (classname === undefined) {
					classname = $("#page_classname").data("class");
				}
				var func = multi_dialog_functions[classname];
				if (func) {
					func(dialog_id + " ");
				}
			}

			var appendarea = res['appendarea'];
			for (key in appendarea) {
				var htmlstr = appendarea[key];
				$(key).html($(key).html() + htmlstr);
				$(key).css({opacity: '0.5'})
				$(key).animate({opacity: '1'}, 'slow');

				$(key).ready(function () {

					// デフォルトのJSを動かす
					var p = $(key).parents(".multi_dialog");
					if (p.length > 0) {
						var dialog_id = "#" + $(key).parents(".multi_dialog").attr("id");
						multi_dialog_functions["__all__"](dialog_id, true);
					} else {
						multi_dialog_functions["__all__"]("body", true);
					}

					// クラスのJSを動かす
					var dialog_id = "#" + $(key).parents(".multi_dialog").attr("id");
					var classname = $(key).parents(".multi_dialog_contents").attr("data-classname");
					var func = multi_dialog_functions[classname];
					if (func) {
						func(dialog_id + " ");
					}

				});
			}


			if (res["console_log"] != null) {
				console.log("  (Server log)");
				for (var md of res["console_log"]) {
					var color_title = "color:" + md["color"] + ";font-weight:bold;"
					console.log("%c" + "  " + md["log"], color_title);
				}
			}

			// 通知(notification)
			if (res["notifications"] != null) {
				for (var md of res["notifications"]) {
					var html = md["html"];
					var width = md["width"];
					var time = md["time"];
					var classname = res["class"];
					ensureClassStylesheet(classname);

					width = get_dialog_width(width);

					notification(classname, html, width, time);
				}
			}

			// Sidemenu
			if (res["sidemenu"] != null) {
				for (var md of res["sidemenu"]) {
					var html = md["html"];
					var width = md["width"];
					var time = md["time"];
					var from = md["from"];
					var classname = res["class"];
					ensureClassStylesheet(classname);

					sidemenu(classname, html, width, time, from);
				}
			}

			// Close sidemenu
			if (res["close_sidemenu"] == true) {
				if (sidemenu_from == 'right') {
					var document_width = $(document).width();
					$('#sidemenu').removeClass('detect_outside_click').animate({'left': document_width + 'px'}, 200);
				} else {
					var sidemenu_width = $('#sidemenu').width();
					$('#sidemenu').removeClass('detect_outside_click').animate({'left': '-' + sidemenu_width + 'px'}, 200);
				}
			}

			// Close Dialog by ID
			if (res["close_dialog_by_id"] != null) {
				for (var md of res["close_dialog_by_id"]) {
					var dialog_id = md["dialog_id"];
					$(dialog_id).fadeOut().remove();
				}
			}

			// Second work area
			if (res["second_work_area"] != null) {
				for (var md of res["second_work_area"]) {
					var html = md["html"];
					var width = md["width"];
					var classname = res["class"];

					second_work_area(classname, html, width);
				}
			}

			// chat
			if (res["chat"] != null) {
				var currentTime = Date.now();
				var c = 0;
				for (var md of res["chat"]) {

					if (md["type"] == "clear") {
						if (md["chatid"] == "all") {
							$("#chat_history").html("");
						} else {
							$(md["chatid"]).remove();
						}
					} else if (md["type"] == "clear_after") {
						$(md["chatid"]).parent().nextAll('.lang_check_area').remove();
					} else {
						var lang_check_area = document.createElement('div');
						var multi_dialog_tag = document.createElement('div');
						var html = md["html"];

						if (md["overwrite"]) {
							let chatid = md['chatid'];
							$(chatid).remove();
						}

						if (md["type"] == "text") {
							$(multi_dialog_tag).addClass("chat-text");
							$(multi_dialog_tag).addClass("lang");
						} else {
							$(multi_dialog_tag).addClass("chat-html");
						}

						var chat_dialog_id = 'chat-html-' + currentTime.toString() + c;

						$(lang_check_area).addClass("lang_check_area");
						$(lang_check_area).attr("data-classname", res["class"]);
						$(multi_dialog_tag).attr('id', chat_dialog_id);
						$(multi_dialog_tag).attr('data-_chatID', 'chat-html-' + currentTime.toString() + c);
						$(multi_dialog_tag).css("float", md["align"]);
						$(multi_dialog_tag).append(html);
						$(lang_check_area).append(multi_dialog_tag);

						(function (element) {
							setTimeout(() => {
								$(element).hide();
								$("#chat_history").append(element);
								translate();
								multi_dialog_functions["__all__"]("body", true);
								$(element).fadeIn(100);
								// move to the bottom of document
								$('html, body').animate({scrollTop: $(document).height()}, 'slow');
							}, c * 100);
						})(lang_check_area);

						c++;
					}
				}


			}

			if (res["popup"] != null) {
				for (var md of res["popup"]) {
					var html = md["html"];
					var width = md["width"];
					var height = md["height"];
					var classname = res["class"];

					popup(classname, width, height, html);
				}
			}

			// login_node
			if (res["login_node"] != null) {
				var md = res["login_node"];
				var room_name = md["room_name"];
				var group_name = md["group_name"];
				var name = md["name"];
				websocket_login(room_name, group_name, name);
			}

			// send_to_node
			if (res["send_to_node"] != null) {

				if (websocket_logined != 1) {
					console.log("Can't send data to node.You must login to node first.");
				}

				for (var md of res["send_to_node"]) {
					//console.log(md);
					var data = md["data"];
					var room_name = md["room_name"];
					var group_name = md["group_name"];
					var user_id = res["user_id"];
					websocket_send(room_name, group_name, user_id, data);
				}
			}

			// ajax
			if (res["ajax"] != null) {
				for (var md of res["ajax"]) {
					let ajax_classname = md["class"];
					let ajax_function = md["function"];
					let post_arr = JSON.parse(md["post_arr"]);

					let fd = new FormData();
					fd.append("class", ajax_classname);
					fd.append("function", ajax_function);
					fd.append("multi_dialog_zindex", multi_dialog_zindex);
					multi_dialog_zindex++;
					if (post_arr != null) {
						for (let key in post_arr) {
							if (!Object.prototype.hasOwnProperty.call(post_arr, key))
								continue;

							const v = post_arr[key];

							// Array -> key[] で複数 append
							if (Array.isArray(v)) {
								for (let i = 0; i < v.length; i++) {
									fd.append(key + "[]", v[i]);
								}
								continue;
							}

							// null/undefined は空文字にして送る（必要に応じて変更）
							if (v === null || typeof v === "undefined") {
								fd.append(key, "");
								continue;
							}

							// Object -> JSON文字列で送る（配列以外のオブジェクトが来る可能性がある場合）
							if (typeof v === "object") {
								fd.append(key, JSON.stringify(v));
								continue;
							}

							// scalar -> そのまま
							fd.append(key, v);
						}
					}

					// 直接呼ぶとどんどん深くなるのを防ぐ
					setTimeout(function () {
						appcon("app.php", fd);
					}, 1);
				}
			}

			// long polling
			if (res["polling"] != null) {
				setTimeout(function () {
					var md = res["polling"];
					startLongPolling(md);
				}, 1);
			}

			// badge
			if (res["badge"] != null) {
				for (var md of res["badge"]) {
					console.log(md);
					var id = md["id"];
					var val = md["val"];
					$("#" + id).html(val);
					if (val > 0) {
						$("#" + id).show();
					} else {
						$("#" + id).hide();
					}
				}
			}

			// Google Map
			if (res["map"] != null) {
				setTimeout(function () {
					// Delay 1sec
					var tag_id = res["map"]["tag_id"];
					var lat = res["map"]["lat"];
					var lng = res["map"]["lng"];
					var zoom = res["map"]["zoom"];
					var markerData = res["map_marker"];
					draw_google_map(tag_id, lat, lng, zoom, markerData);

				}, 1000);
			}

			// マルチダイアログ
			if (res["multi_dialog"] != null) {

				for (var md of res["multi_dialog"]) {
					var dialog_name = md["dialog_name"];
					var html = md["html"];
					var title = md["title"];
					var width = md["width"];
						var cmd = md["cmd"];
						var classname = res["class"];
						var testserver = md["testserver"];
						var post_arr = md["post_arr"];
						var mdx = md["multi_dialog_zindex"];
						var forcopy = md["forcopy"];
						var fixed_bar = md["fixed_bar"];
						var options = md["options"];

					if (cmd == "close") {
						classname = md["class"];
						var dialog_id = "#multi_dialog_" + classname + "_" + dialog_name;
						$(dialog_id).remove();
						refresh_multi_dialog_modal_cover();
						continue;

					} else {

							if (mdx == null) {
								mdx = multi_dialog_zindex;
							}
							ensureClassStylesheet(classname);
							multi_dialog(dialog_name, html, title, width, classname, testserver, mdx, fixed_bar, options, fadeflg, forcopy);
							multi_dialog_zindex++;
					}
				}
			}

			// タブの追加
			if (res["add_tab"] != null) {
				for (var md of res["add_tab"]) {
					let dialog_id = "#multi_dialog_" + res["class"] + "_" + md["dialog_name"];
					// 同じtabnameのタブがすでにあるかチェックする
					var flg = true;
					$(dialog_id).find(".multi_dialog_tab_area").find(".md_tab").each(function () {
						if ($(this).data("tabname") == md["tabname"]) {
							flg = false;
						}
					});

					// 同じtabnameがなかったら、タブを追加する
					if (flg) {
						let tab = document.createElement('div');
						$(tab).addClass("md_tab");
						$(tab).attr("data-tabname", md["tabname"]);
						$(tab).html(md["title"]);
						$(dialog_id).find(".multi_dialog_tab_area").append(tab);

						if (md["selected"]) {
							$(tab).addClass("md_tab_select");
						}

						// クリックイベントを登録
						let post_arr = md["post_arr"];
						$(tab).on("click", function (e) {

							// タブの選択を全て解除
							$(dialog_id).find(".multi_dialog_tab_area").find(".md_tab").each(function () {
								$(this).removeClass("md_tab_select");
							});

							let fd = new FormData();
							for (let key in post_arr) {
								fd.append(key, post_arr[key]);
							}
							;

							$(tab).addClass("md_tab_select");

							appcon("app.php", fd);
						});
					}


				}
			}

			// メインエリア 
			if (res["work_area"] != null) {

				var md = res["work_area"];
				var dialog_name = md["dialog_name"];
				var html = md["html"];
				var title = md["title"];
				var classname = res["class"];
				var testserver = md["testserver"];
				var post_arr = md["post_arr"];

				var multi_dialog_tag = document.createElement('div');
				$(multi_dialog_tag).attr("id", "multi_dialog_" + classname + "_" + dialog_name);
				$(multi_dialog_tag).attr("data-classname", classname);
				$(multi_dialog_tag).addClass("lang_check_area");
				$(multi_dialog_tag).addClass("getting_dialog_id");
				$(multi_dialog_tag).append('<div class="work_area_title lang">' + title + '</div>');
				$(multi_dialog_tag).append(html);
				$("#work_area").hide().html("").append(multi_dialog_tag).show();

				$(multi_dialog_tag).ready(function () {
					var dialog_id = "#multi_dialog_" + classname + "_" + dialog_name;

					// デフォルトのJSを動かす
					multi_dialog_functions["__all__"](dialog_id, true);

					// クラスのJSを動かす
					var func = multi_dialog_functions[classname];
					if (func) {
						func(dialog_id + " ");
					}

					// スクロールイベント
						var tag_ajax_auto = $(multi_dialog_tag).find(".ajax-auto");
						$(window).off("scroll.ajax_auto_work_area");
						if (tag_ajax_auto.length > 0) {
							$(window).on("scroll.ajax_auto_work_area", function () {
								ajax_auto_exe(dialog_id);
							});
						}
						ajax_auto_exe(dialog_id);
					});
				}

			if (res["chartjs"] != null) {
				setTimeout(function () {
					for (var md of res["chartjs"]) {
						var tag_id = md["tag_id"];
						var chart = md["chart"];

						// IDが複数の場合も対応
						var elements = document.querySelectorAll('#' + tag_id);
						if (elements.length === 1) {
							// IDが1つの場合、その要素をcanvasに入れる
							var canvas = elements[0];
						} else if (elements.length > 1) {
							// IDが複数の場合、最後の要素をcanvasに入れる
							var canvas = elements[elements.length - 1];
						} else {
							console.error(`Error: Element with id '${tag_id}' not found.`);
						}

						if (canvas) {
							var ctx = canvas.getContext('2d');
							var mychart = new Chart(ctx, chart);
						}
					}
				}, 100);
			}


			if (nextfunction) {
				nextfunction(res);
			}

		} catch (e) {
			append_debug_window(e, data, "error");
			mdx = multi_dialog_zindex;
			html = "<div class=\"error error_window_message\">" + escapeHtml(data) + "</div>";
			multi_dialog("error", html, "ERROR", 600, "error", testserver, mdx);
			multi_dialog_zindex++;

		}

		dfd.resolve();

	}).fail(function ($xhr) {

		data = $xhr.responseText;
		if (data != undefined) {
			//alert(data);
		}

		dfd.resolve();
	});

	return dfd.promise();
}

/*
 * 通知（Notification)
 */
function notification(classname, html, width, time) {
	var multi_dialog_tag = document.createElement('div');
	$(multi_dialog_tag).addClass("notification");
	$(multi_dialog_tag).addClass("lang_check_area");
	$(multi_dialog_tag).attr("data-classname", classname);
	$(multi_dialog_tag).css("width", width);
	$(multi_dialog_tag).append(html);
	$(multi_dialog_tag).fadeOut(0);
	$("#multi_dialog").append(multi_dialog_tag);

	// z-index
	$(multi_dialog_tag).css("z-index", "9999999999");

	translate();

	$(multi_dialog_tag).fadeIn(200, function () {
		setTimeout(function () {
			$(multi_dialog_tag).fadeOut(200, function () {
				$(this).remove();
			});
		}, time * 1000);
	});
}



function sidemenu(classname, html, width, time, from) {

	// for closing
	sidemenu_from = from;

	$("#sidemenu").remove();

	var multi_dialog_tag = document.createElement('div');
	$(multi_dialog_tag).addClass("sidemenu");
	$(multi_dialog_tag).attr("id", "sidemenu");
	$(multi_dialog_tag).addClass("lang_check_area");
	$(multi_dialog_tag).attr("data-classname", classname);
	$(multi_dialog_tag).css("width", width);
	$(multi_dialog_tag).append(html);
	// $(multi_dialog_tag).fadeOut(0);
	$(multi_dialog_tag).css("z-index", "9999999999");
	$(multi_dialog_tag).css("background", "white");
	$("#multi_dialog").append(multi_dialog_tag);

	translate();

	if (from == 'right') {
		var document_width = $(document).width();
		$(multi_dialog_tag).css("left", document_width + "px");
		$(multi_dialog_tag).addClass('detect_outside_click').animate({'left': document_width - width + "px"}, time);
		$(multi_dialog_tag).css("box-shadow", "-5px 0px 5px #CCC");
	} else {
		$(multi_dialog_tag).css("left", "-" + width + "px");
		$(multi_dialog_tag).addClass('detect_outside_click').animate({'left': '0'}, time);
		$(multi_dialog_tag).css("box-shadow", "5px 0px 5px #CCC");
	}

}

var flg_second_work_area = 0;
var flg_second_work_area_hidden = 0;
var second_work_area_scroll_restore_top = null;

function bring_second_work_area_to_front() {
	var baseZ = multi_dialog_zindex;
	$("#work_area_second").css("z-index", baseZ);
	$("#work_area_second_show_button").css("z-index", baseZ + 1);
	multi_dialog_zindex += 2;
}

function adjust_second_work_area_layout() {
	if (flg_second_work_area !== 1) {
		return;
	}
	var panelMargin = 10;
	var windowWidth = $(window).width();
	var requestedWidth = parseInt($("#work_area_second").attr("data-panel-width"), 10) || 400;
	var panelWidth = Math.max(260, Math.min(requestedWidth, windowWidth - 20));
	var rightPosition = flg_second_work_area_hidden === 1 ? -1 * (panelWidth + 12) : panelMargin;

	$("#work_area_second").css({
		"top": panelMargin,
		"bottom": panelMargin,
		"width": panelWidth,
		"right": rightPosition
	});
}

$(window).resize(function () {
	adjust_second_work_area_layout();
});

function second_work_area(classname, html, w) {
	var wasAlreadyOpen = (flg_second_work_area === 1 && flg_second_work_area_hidden === 0 && $("#work_area_second").is(":visible"));
	flg_second_work_area = 1;
	flg_second_work_area_hidden = 0;
	bring_second_work_area_to_front();
	$("#work_area_second").attr("data-panel-width", w);
	$("#work_area_second").addClass("work_area_second_active");
	$("#work_area_second").html("");

	var action_bar = document.createElement("div");
	$(action_bar).addClass("work_area_second_action_bar");
	var hide_button = document.createElement("div");
	$(hide_button).addClass("work_area_second_action_button");
	$(hide_button).append('<span class="material-symbols-outlined">chevron_right</span>');
	var close_button = document.createElement("div");
	$(close_button).addClass("work_area_second_action_button");
	$(close_button).append('<span class="material-symbols-outlined">close</span>');
	$(action_bar).append(hide_button).append(close_button);

	var body = document.createElement("div");
	$(body).addClass("work_area_second_body");
	var multi_dialog_tag = document.createElement("div");
	$(multi_dialog_tag).addClass("lang_check_area");
	$(multi_dialog_tag).attr("data-classname", classname);
	$(multi_dialog_tag).addClass("getting_dialog_id");
	$(multi_dialog_tag).html(html);
	$(body).append(multi_dialog_tag);

	$("#work_area_second").append(action_bar).append(body);

	if ($("#work_area_second_show_button").length === 0) {
		$("body").append('<div id="work_area_second_show_button"><span class="material-symbols-outlined">chevron_left</span></div>');
	}

	$(hide_button).off("click").on("click", function () {
		flg_second_work_area_hidden = 1;
		var panelWidth = $("#work_area_second").outerWidth() || 0;
		$("#work_area_second").stop(true, true).animate({"right": -1 * (panelWidth + 12)}, 260, "swing");
		$("#work_area_second_show_button").stop(true, true).fadeIn(180);
	});
		$(close_button).off("click").on("click", function () {
			var fd = new FormData();
			fd.append("class", "db_exe");
			fd.append("function", "close_second_work_area");
			appcon("app.php", fd);
	});
	$("#work_area_second_show_button").off("click").on("click", function () {
		flg_second_work_area_hidden = 0;
		bring_second_work_area_to_front();
		$(this).stop(true, true).fadeOut(120);
		$("#work_area_second").stop(true, true).animate({"right": "10px"}, 260, "swing");
	});

	adjust_second_work_area_layout();
	var panelWidth = $("#work_area_second").outerWidth() || 0;
	if (wasAlreadyOpen) {
		$("#work_area_second").show().stop(true, true).css("right", "10px");
	} else {
		$("#work_area_second").show().css("right", -1 * (panelWidth + 12)).stop(true, true).animate({"right": "10px"}, 280, "swing");
	}

	var dialog_id = "#work_area_second";
	translate();

	// デフォルトのJSを動かす
	multi_dialog_functions["__all__"](dialog_id, true);

	// クラスのJSを動かす
	var func = multi_dialog_functions[classname];
	if (func) {
		func(dialog_id + " ");
	}

	// ajax-auto でサイドパネルを再描画した際はスクロール位置を復元する
	if (second_work_area_scroll_restore_top !== null) {
		var restoreTop = second_work_area_scroll_restore_top;
		second_work_area_scroll_restore_top = null;
		setTimeout(function () {
			$("#work_area_second .work_area_second_body").scrollTop(restoreTop);
		}, 0);
	}
}


function remove_second_work_area() {
	flg_second_work_area = 0;
	flg_second_work_area_hidden = 0;
	second_work_area_scroll_restore_top = null;
	$("#work_area_second .work_area_second_body").off("scroll.ajax_auto");
	$("#work_area_second").removeClass("work_area_second_active");
	$("#work_area_second").removeAttr("style").html("").hide();
	$("#work_area_second_show_button").remove();
	$(".active_indicator").removeClass("indicator_active");
}


function popup(classname, width, height, html) {


	$('#popup').remove();

	width = get_dialog_width(width);

	var multi_dialog_tag = document.createElement('div');
	$(multi_dialog_tag).addClass("popup");
	$(multi_dialog_tag).attr("id", "popup");
	$(multi_dialog_tag).addClass("lang_check_area");
	$(multi_dialog_tag).attr("data-classname", classname);
	$(multi_dialog_tag).css("width", width);
	$(multi_dialog_tag).css("height", height);
	$(multi_dialog_tag).append(html);
	// $(multi_dialog_tag).fadeOut(0);
	$(multi_dialog_tag).css("z-index", "9999999999");
	$(multi_dialog_tag).css("background", "white");
	$("#multi_dialog").append(multi_dialog_tag);

	translate();
	$('#popup').addClass('detect_outside_click').css('opacity', '1').css('display', 'block');

}



/*
 * マルチダイアログ処理
 */
var multi_dialog_zindex = 100;
var multi_dialog_reflesh_time = Math.floor(Date.now() / 1000);

function ensure_multi_dialog_modal_cover() {
	if ($("#multi_dialog_modal_cover").length === 0) {
		var cover = document.createElement("div");
		$(cover).attr("id", "multi_dialog_modal_cover");
		$(cover).css({
			"display": "none",
			"position": "fixed",
			"left": "0",
			"top": "0",
			"width": "100vw",
			"height": "100vh",
			"background": "rgba(90,90,90,0.45)"
		});
		$("body").append(cover);
	}
}

function refresh_multi_dialog_modal_cover() {
	ensure_multi_dialog_modal_cover();
	var modalDialogs = $("#multi_dialog .multi_dialog[data-modal_flg='1']");
	if (modalDialogs.length === 0) {
		$("#multi_dialog_modal_cover").hide();
		return;
	}
	var highest = 0;
	modalDialogs.each(function () {
		var zi = parseInt($(this).css("z-index"), 10);
		if (isNaN(zi)) {
			zi = 0;
		}
		if (zi > highest) {
			highest = zi;
		}
	});
	if (highest < 100) {
		highest = 100;
	}
	$("#multi_dialog_modal_cover").css("z-index", highest - 1).show();
	modalDialogs.each(function () {
		var zi = parseInt($(this).css("z-index"), 10);
		if (isNaN(zi) || zi <= highest - 1) {
			$(this).css("z-index", highest + 1);
		}
	});
}

function multi_dialog(dialog_name, contents, title, width, getclassname, testserver, mdz, fixed_bar = "", options = [], fadeflg = true, forcopy = "") {


	var exe_classname = getclassname;
	var dialog_id = "#multi_dialog_" + exe_classname + "_" + dialog_name;
	var is_modal = false;
	if (options != null) {
		if (options["modal_flg"] == 1 || options["modal_flg"] === true || options["modal"] == 1 || options["modal"] === true) {
			is_modal = true;
		}
	}

	var multi_dialog_tag = null;

	var title_display = '<span class="lang">' + title + '</span>';

	function syncCopyButton($dlg, ts, forcopyValue) {
		var tsFlg = (ts === true || ts === 1 || String(ts).toLowerCase() === "true");
		var $titleArea = $dlg.find(".multi_dialog_title_area").first();
		if ($titleArea.length === 0) {
			return;
		}
		var value = String(forcopyValue || "").trim();
		$titleArea.attr("data-forcopy", value);
		var $copy = $titleArea.find(".multi_dialog_copy_title").first();
		if (!tsFlg || value === "") {
			$copy.remove();
			return;
		}
		if ($copy.length === 0) {
			$copy = $('<span class="multi_dialog_copy_title" title="Copy class/function">⧉</span>');
			var $close = $titleArea.find(".multi_dialog_close").first();
			if ($close.length > 0) {
				$close.before($copy);
			} else {
				$titleArea.append($copy);
			}
		}
		$copy.attr("data-copy-value", value);
	}

	if ($(dialog_id).length) {

		// 一旦非表示(更新してから３以内は非表示にしない)
		var tmp_now = Math.floor(Date.now() / 1000);
		if (fadeflg) {
			if (tmp_now > (multi_dialog_reflesh_time + 3)) {
				$(dialog_id + " .multi_dialog_contents").fadeOut({duration: 0, });
				multi_dialog_reflesh_time = tmp_now;
			}
		}

		// コンテンツを入れかえる
		$(dialog_id + " .multi_dialog_contents").html(contents);

		// FIXED BARを入れる
		$(dialog_id + " .multi_dialog_fixed_bar").html(fixed_bar);

		// タイトルを入れる
		if (title != "") {
			var $innerTitle = $(dialog_id + " .multi_dialog_innder_title");
			if ($innerTitle.length > 0) {
				$innerTitle.html(title_display);
				$innerTitle.show();
			} else {
				var $titleArea = $(dialog_id + " .multi_dialog_title_area").first();
				if ($titleArea.length > 0) {
					var $closeButton = $titleArea.find(".multi_dialog_close").first();
					var $copyButton = $titleArea.find(".multi_dialog_copy_title").first();
					$titleArea.contents().not($closeButton).not($copyButton).remove();
					$titleArea.prepend(title_display);
				}
			}
		} else {
			var $innerTitleHide = $(dialog_id + " .multi_dialog_innder_title");
			if ($innerTitleHide.length > 0) {
				$innerTitleHide.hide();
			} else {
				var $titleAreaHide = $(dialog_id + " .multi_dialog_title_area").first();
				if ($titleAreaHide.length > 0) {
					var $closeButtonHide = $titleAreaHide.find(".multi_dialog_close").first();
					var $copyButtonHide = $titleAreaHide.find(".multi_dialog_copy_title").first();
					$titleAreaHide.contents().not($closeButtonHide).not($copyButtonHide).remove();
				}
			}
		}
		syncCopyButton($(dialog_id), testserver, forcopy);

		// z-indexの設定
		$(dialog_id).css("z-index", mdz);
		$(dialog_id).attr("data-modal_flg", is_modal ? "1" : "0");

		$(contents).ready(function () {

			if (fadeflg) {
				$(dialog_id + " .multi_dialog_contents").fadeIn({duration: 500});
			}

			// デフォルトのJSを動かす
			multi_dialog_functions["__all__"](dialog_id);

			// クラスのJSを動かす
			var func = multi_dialog_functions[exe_classname];
			if (func) {
				func(dialog_id + " ");
			}

			// 高さを修正
			set_multidialog_height(dialog_id);
			refresh_multi_dialog_modal_cover();

		});

		return;

	} else {
		//-----------------------
		// 新しいウィンドウを開く
		//-----------------------

		// ダイアログ全体
		multi_dialog_tag = document.createElement('div');
		$(multi_dialog_tag).attr("id", "multi_dialog_" + exe_classname + "_" + dialog_name);
		$(multi_dialog_tag).attr("data-dialog_name", dialog_name);
		$(multi_dialog_tag).attr("data-classname", exe_classname);
		$(multi_dialog_tag).addClass("multi_dialog");
		$(multi_dialog_tag).addClass("getting_dialog_id");
		$(multi_dialog_tag).attr("data-modal_flg", is_modal ? "1" : "0");

		// タイトル部分
		var dialog_html = '<div class="multi_dialog_title_area lang_check_area" data-classname="' + exe_classname + '" data-forcopy="">' + title_display + '<div class="multi_dialog_close">X</div></div>';

		// タブ
		var tab_area = document.createElement('div');
		$(tab_area).addClass("multi_dialog_tab_area");
		$(tab_area).addClass("lang_check_area");
		$(tab_area).attr("data-classname", exe_classname);

		// 固定バー
		var fixed_bar_tag = $('<div class="multi_dialog_fixed_bar lang_check_area class_style_' + exe_classname + '"></div>');
		fixed_bar_tag.attr("data-classname", exe_classname);
		fixed_bar_tag.html(fixed_bar);

		// タブのコンテンツのコンテナ
		var container = document.createElement('div');
		$(container).addClass("tab_container");

		// ダイアログに入れていく
		$(multi_dialog_tag).append(dialog_html);
		$(multi_dialog_tag).append(tab_area);
		//$(multi_dialog_tag).append(fixed_bar_tag);
		$(multi_dialog_tag).append(container);

		$(container).fadeOut({duration: 0, });

		// HTMLに入れる
		$("#multi_dialog").append(multi_dialog_tag);

		// Draggable
		$(multi_dialog_tag).draggable({
			handle: ".multi_dialog_title_area",
			start: function () {
				$(this).css("z-index", multi_dialog_zindex);
				multi_dialog_zindex++;
			},
			stop: function () {
				var st = $(window).scrollTop();
				if ($(this).offset().top < st) {
					$(this).offset({top: st});
				}
				if ($(this).offset().top > st + $(window).height()) {
					$(this).offset({top: st + $(window).height() - 100});
				}
			}
		});

		// Resizable
		$(multi_dialog_tag).resizable();

		// 拡大アイコンを消す
		$(multi_dialog_tag).find(".ui-resizable-handle").removeClass("ui-icon");

		// Click
		$(multi_dialog_tag).on("click", function (e) {
			$(dialog_id).css("z-index", multi_dialog_zindex);
			multi_dialog_zindex++;
		});

		// クローズイベント
		$(multi_dialog_tag).on("click", ".multi_dialog_close", function (e) {
			$(multi_dialog_tag).resizable("destroy");
			$(multi_dialog_tag).draggable("destroy");
			$(multi_dialog_tag).off("click", ".multi_dialog_close");
			$(multi_dialog_tag).remove();
			refresh_multi_dialog_modal_cover();
		});

		// class/function のコピー
		$(multi_dialog_tag).on("click", ".multi_dialog_copy_title", function (e) {
			e.preventDefault();
			e.stopPropagation();
			var value = String($(this).attr("data-copy-value") || "").trim();
			if (value === "") {
				return;
			}
			var done = function () {
				$(this).addClass("copied");
				setTimeout(() => {
					$(this).removeClass("copied");
				}, 700);
			}.bind(this);
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(value).then(done).catch(function () {
					var ta = document.createElement("textarea");
					ta.value = value;
					ta.style.position = "fixed";
					ta.style.left = "-9999px";
					document.body.appendChild(ta);
					ta.focus();
					ta.select();
					try {
						document.execCommand("copy");
						done();
					} catch (err) {
						// noop
					}
					document.body.removeChild(ta);
				});
				return;
			}
			var ta = document.createElement("textarea");
			ta.value = value;
			ta.style.position = "fixed";
			ta.style.left = "-9999px";
			document.body.appendChild(ta);
			ta.focus();
			ta.select();
			try {
				document.execCommand("copy");
				done();
			} catch (err2) {
				// noop
			}
			document.body.removeChild(ta);
		});

		// ウィンドウのサイズ
		var dialog_window_size = get_dialog_width(width);
		$(multi_dialog_tag).css("width", dialog_window_size);

		var windowwidth = $("body").width();
		//$(multi_dialog_tag).css("top", 120 + Math.random() * 20);
		$(multi_dialog_tag).css("top", 60 + Math.random() * 5);
		$(multi_dialog_tag).css("left", (windowwidth - dialog_window_size) / 2);

		// スクロールとコンテンツを入れる
		var scroll_tag = document.createElement("div");
		var contents_tag = document.createElement("div");
		$(contents_tag).addClass("multi_dialog_contents");
		$(contents_tag).addClass("lang_check_area");
		$(contents_tag).attr("data-classname", exe_classname);
		$(contents_tag).html(contents);
		$(scroll_tag).append(fixed_bar_tag);
		$(scroll_tag).append(contents_tag);
		$(scroll_tag).addClass("multi_dialog_scroll");
		$(container).append(scroll_tag);

		// 表示
		$(container).fadeIn({duration: 200});

		// z-index
		$(dialog_id).css("z-index", multi_dialog_zindex);
		multi_dialog_zindex++;

			$(scroll_tag).ready(function () {
				syncCopyButton($(dialog_id), testserver, forcopy);
				// デフォルトのJSを動かす
			multi_dialog_functions["__all__"](dialog_id + " ");

			// クラスのJSを動かす
			var func = multi_dialog_functions[exe_classname];
			if (func) {
				func(dialog_id + " ");
			}

			// 高さの設定
			set_multidialog_height(dialog_id);
			refresh_multi_dialog_modal_cover();

		});

}
}



function set_multidialog_height(dialog_id) {
	if (!$(dialog_id).data("ui-resizable")) {
		$(dialog_id).resizable();
	}
	$(dialog_id).resizable("disable");

	// いまのスクロール位置を保存
	const $dlg = $(dialog_id);
	const $sc = $dlg.find('.multi_dialog_scroll');
	const winScroll = $(window).scrollTop();
	const innerScroll = $sc.length ? $sc.scrollTop() : 0;

	setTimeout(function () {
		// 一度autoにする（レイアウト再計算）
		$dlg.css("height", "auto");

		const height_dialog = $dlg.outerHeight();
		const windowHeight = window.innerHeight;
		const dialogTop = parseInt($dlg.css("top"), 10) || 0;
		const maxHeight = (windowHeight - dialogTop) * 0.9;

		$dlg.css("max-height", maxHeight);
		$sc.css("max-height", maxHeight - 30);

		if (height_dialog > maxHeight) {
			$dlg.height(maxHeight);
		}

		// ★スクロール位置を復元（ジャンプ防止）
		if ($sc.length)
			$sc.scrollTop(innerScroll);
		$(window).scrollTop(winScroll);

		ajax_auto_exe(dialog_id);
		$(dialog_id).resizable("enable");
	}, 500);
}


// ダウンロードリンク
$("body").on("click", ".download-link", function (e) {

	e.preventDefault();

	var form = $(this).data("form");
	if (form === undefined) {
		var fc = $(this).closest("form");
		if (fc !== undefined) {
			var f = fc.get(0);
			// 日付を数値ににしてFormDataを取得
			var fd = get_formdata_with_strtotime(f);
		} else {
			var fd = new FormData();
		}
	} else {
		var f = $("#" + form).get(0);
		// 日付を数値ににしてFormDataを取得
		var fd = get_formdata_with_strtotime(f);
	}

	var v = ($(this).attr('data-open_new_tab') || '').toString().toLowerCase();
	var open_new_tab = (v === 'true' || v === '1' || v === 'yes');

	var url = $(this).data("url");
	if (url == undefined) {
		url = "app.php";
	}
	// data-filename は jQuery.data() のキャッシュ状況に依存せず属性値を優先して取得する
	var filename = $(this).attr("data-filename");
	if (filename === undefined || filename === null || filename === "") {
		filename = $(this).data("filename");
	}
	if (filename === undefined || filename === null) {
		filename = "";
	} else {
		filename = String(filename);
	}
	var datalist = $(this).data();
	for (key in datalist) {
		fd.append(key, datalist[key]);
	}
	
	// CHANGE: invoke-class / invoke-function を fd に反映
	var invokeClass = $(this).attr("invoke-class");
	if (invokeClass !== undefined && invokeClass !== "") {
	  fd.append("class", invokeClass);
	}

	var invokeFunction = $(this).attr("invoke-function");
	if (invokeFunction !== undefined && invokeFunction !== "") {
	  fd.append("function", invokeFunction);
	}

	// Timezone
	var intl_tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
	fd.append("_timezone", intl_tz);

	// open_new_tab は blob URL を使わず、POSTをそのまま新規タブへ送る
	// （PDFビューア保存時のUUID名化を回避）
	if (open_new_tab) {
		var posted = submit_download_in_new_tab(url, fd);
		if (posted) {
			return;
		}
	}

	// 同期処理
	$.when(
			modal_download(url, fd, filename, open_new_tab)
			).done(function (data) {
	});
});


//--------------------------------------------
// プラグイン用データ送信＆ダウンロード
//--------------------------------------------
function modal_download(url, fd, fileName, open_new_tab = false) { // CHANGE: 引数追加

	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);

	xhr.onprogress = function (evt) {
		$("#download_view").show();
		if (evt.total > 0) {
			var load = 100 * evt.loaded / evt.total;
			$('#download_message').html(
					"Downloading...   " +
					Math.round(evt.loaded / 1000) + " / " +
					Math.round(evt.total / 1000) + "kbyte"
					);
			$('#download_progress').css({'width': load + '%'});
		} else {
			$('#download_message').html("");
			$('#download_progress').html(Math.round(evt.loaded / 1000) + " kbyte");
			$('#download_progress').css({'width': '100%'});
		}
	};

	xhr.responseType = 'arraybuffer';

	xhr.onload = function () {

		var bytes = new Uint8Array(this.response);
		var resolvedFileName = (function () {
			if (fileName !== undefined && fileName !== null && String(fileName) !== "") {
				return String(fileName);
			}
			var cd = xhr.getResponseHeader("Content-Disposition") || "";
			if (cd !== "") {
				var mUtf8 = cd.match(/filename\*=UTF-8''([^;]+)/i);
				if (mUtf8 && mUtf8[1]) {
					try {
						return decodeURIComponent(mUtf8[1].trim());
					} catch (e) {
					}
				}
				var mQuoted = cd.match(/filename=\"([^\"]+)\"/i);
				if (mQuoted && mQuoted[1]) {
					return mQuoted[1].trim();
				}
				var mPlain = cd.match(/filename=([^;]+)/i);
				if (mPlain && mPlain[1]) {
					return mPlain[1].trim().replace(/^\"|\"$/g, "");
				}
			}
			return "download";
		})();

		// CHANGE: 取得した先頭200KBを console.log に出力（1回のみ）
		(function logFirst300() {
			var MAX = 300;
			var len = Math.min(bytes.length, MAX);
			var head = bytes.subarray(0, len);

			// バイナリとして確認
			console.log('[download head bytes]', head);

			// テキストとして確認（デバッグ用）
			try {
				var text = new TextDecoder('utf-8', {fatal: false}).decode(head);
				console.log('[download head text]', text);
			} catch (e) {
				console.log('[download head text decode error]', e);
			}
		})();

		var lower = (resolvedFileName || '').toLowerCase();
		var isPdf = lower.slice(-4) === '.pdf';
		var mime = isPdf ? 'application/pdf' : 'application/octet-stream';
		var blob = new Blob([bytes], {type: mime});
		var blobSource = blob;
		try {
			// open_new_tab でも保存名を引き継ぎやすいよう File(name付き) を優先
			blobSource = new File([bytes], resolvedFileName, {type: mime});
		} catch (e) {
			blobSource = blob;
		}

		// IE系は従来通り
		if (userAgent.indexOf('msie') != -1) {
			window.navigator.msSaveBlob(blob, resolvedFileName);
			$("#download_view").hide();
			$('#download_progress').css({'width': '0%'});
			return;
		}

		var objUrl = (window.URL || window.webkitURL).createObjectURL(blobSource);

		// CHANGE: ここから「必ず一度だけ」実行
		var opened = false;

		if (open_new_tab) {
			// 新しいタブで開く
			var w = null;
			try {
				w = window.open(objUrl, '_blank', 'noopener');
			} catch (e) {
				w = null;
			}

			if (w && typeof w.closed !== 'undefined') {
				opened = true;
			}

			// 表示完了を待ってから解放
			setTimeout(function () {
				(window.URL || window.webkitURL).revokeObjectURL(objUrl);
			}, 60 * 1000);

		} else {
			// ダウンロード
			var a = document.createElement('a');
			a.download = resolvedFileName;
			a.href = objUrl;
			document.body.appendChild(a);
			a.click();
			document.body.removeChild(a);

			setTimeout(function () {
				(window.URL || window.webkitURL).revokeObjectURL(objUrl);
			}, 10 * 1000);
		}

		$("#download_view").hide();
		$('#download_progress').css({'width': '0%'});
	};

	xhr.send(fd);
	return false;
}

function submit_download_in_new_tab(url, fd) {
	if (!(fd instanceof FormData)) {
		return false;
	}

	var tempForm = document.createElement("form");
	tempForm.method = "POST";
	tempForm.action = url;
	tempForm.target = "_blank";
	tempForm.style.display = "none";

	var hasBinary = false;
	fd.forEach(function (value, key) {
		if ((typeof File !== "undefined" && value instanceof File)
				|| (typeof Blob !== "undefined" && value instanceof Blob)) {
			hasBinary = true;
			return;
		}
		var input = document.createElement("input");
		input.type = "hidden";
		input.name = key;
		input.value = value == null ? "" : String(value);
		tempForm.appendChild(input);
	});

	// バイナリを含む場合は従来のXHR経由にフォールバック
	if (hasBinary) {
		return false;
	}

	document.body.appendChild(tempForm);
	tempForm.submit();
	document.body.removeChild(tempForm);
	return true;
}


//$(function () {
//	$('form').attr('autocomplete', 'off');
//});


//----------------------------
// テキストボックスに３桁表示
//----------------------------
(function ($) {
	$.fn.add_number_format = function () {
		return this.each(function () {
			// Do something to each element here.
			$(this).wrap('<div class="display_number_area"></div>');
			$(this).before('<div class="display_number"></div>');
			$(this).css("text-align", "right");
			var data = $(this).val();
			if (data != "") {
				var sanketa = String(data).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');

			} else {
				var sanketa = 0;
			}
			var dn = $(this).parent().find(".display_number");
			dn.html(sanketa);

			$(this).on("keyup", function (e) {
				var data = $(this).val();
				if (data != "") {
					var sanketa = String(data).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');

				} else {
					var sanketa = 0;
				}
				var dn = $(this).parent().find(".display_number");
				dn.html(sanketa);
			});
		});
	};

})(jQuery);


//エンターキーでフォーム送信を無効
$(function () {
	$("body").on("keydown", "input", function (e) {
		if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
			return false;
		} else {
			return true;
		}
	});
});

//URLパラメーターを取得
function getURLParam(name) {
	var url = window.location.href;
	name = name.replace(/[\[\]]/g, "\\$&");
	var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			results = regex.exec(url);
	if (!results)
		return null;
	if (!results[2])
		return '';
	return decodeURIComponent(results[2].replace(/\+/g, " "));
}

//----------------------------
// テキストボックスに文字数表示
//----------------------------
(function ($) {
	$.fn.text_size_limit = function (options) {
		return this.each(function () {

			if ($(this).hasClass("wordcounter")) {

				$(this).removeClass("wordcounter");
				if (!$(this).parent().hasClass("wordcounter_area")) {
					$(this).wrap('<div class="wordcounter_area"></div>');
				}
				$(this).addClass("wordcounter_textarea");
				var $area = $(this).parent(".wordcounter_area");
				$area.find(".wordcounter_display").remove();
				$area.append('<div class="display_number wordcounter_display"></div>');

				var f = function (obj) {
					var max = $(obj).data("counter_max");
					if (max == null) {
						max = $(obj).data("max");
						if (max == null) {
							append_debug_window("Wordcounter needs data-counter_max parameter in tag", null, "error");
						}
					}

					var count = get_utf8_bytes($(obj).val());
					count = max - count;
					var $display = $(obj).parent(".wordcounter_area").find(".wordcounter_display");
					$display.text(count + " bytes");
					if (count <= 0) {
						$display.css("color", "red");
						$display.text("The number of characters is over.");
					} else {
						$display.css("color", "#bbbbbb");
						$display.text(count + " bytes");
					}
				}

				// １回実行
				f(this);

				$(this).on("keyup", function (e) {
					f(this);
				});
			}
		});
	};
})(jQuery);

//----------------------------
// UTFのバイト数を計算
//----------------------------
function get_utf8_bytes(str) {
	var count = 0;
	for (var i = 0; i < str.length; ++i) {
		var cp = str.charCodeAt(i);
		if (cp <= 0x007F) {
			// U+0000 - U+007F
			count += 1;
		} else if (cp <= 0x07FF) {
			// U+0080 - U+07FF
			count += 2;
		} else if (cp <= 0xD7FF) {
			// U+0800 - U+D7FF
			count += 3;
		} else if (cp <= 0xDFFF) {
			// U+10000 - U+10FFFF
			//
			// 0xD800 - 0xDBFF (High Surrogates)
			// 0xDC00 - 0xDFFF (Low Surrogates)
			count += 2;
		} else if (cp <= 0xFFFF) {
			// U+E000 - U+FFFF
			count += 3;
		} else {
			// undefined code point in UTF-16
			// do nothing
		}
	}
	return count;
}




var multi_dialog_functions = {};

function append_function_dialog(classname, func) {
	multi_dialog_functions[classname] = func;
}



function ajax_auto_exe(dialog_id) {

	var parent_obj = $(dialog_id);
	if (parent_obj.length > 0) {
		var parent_height = parent_obj.height() + 200;
		var parent_top = parent_obj.offset().top;
		var parent_position = parent_height + parent_top;
	}
	var tags = $(dialog_id + " .ajax-auto");
	if (dialog_id === "body") {
		// Keep main/body infinite-scroll independent from the side panel.
		tags = tags.not("#work_area_second .ajax-auto");
	}
	tags = tags.filter(":visible");

	//console.log("parent_height=" + parent_height + " parent_top=" + parent_top + " parent_position=" + parent_position);

	tags.each(function (index, element) {
		var top = $(element).offset().top;

		if (top < $(window).scrollTop() + $(window).height()) {

			if (parent_position === undefined || parent_position > top) {
				// Dialogの中に設置した .ajax-auto タグが表示された

				//console.log("ajax_auto_exe");
				//console.log("element offset top:" + top)

				var form = $(element).data("form");
				if (form === undefined) {
					var fd = new FormData();
				} else {
					var fd = new FormData(parent_obj.find("#" + form).get(0));
				}

				var url = $(element).data("url");
				if (url === undefined) {
					url = "app.php";
				}
				var datalist = $(this).data();
				for (key in datalist) {
					fd.append(key, datalist[key]);
				}

				element.remove();

				setTimeout(function () {
					appcon("app.php", fd);
				}, 1);

			}
		}
	});

}

function ajax_auto_exe_in_scroll_container(dialog_id, container_selector) {
	var parent_obj = $(dialog_id);
	var container = $(container_selector).first();
	if (parent_obj.length === 0 || container.length === 0) {
		return;
	}

	var tags = parent_obj.find(".ajax-auto");
	var containerTop = container.scrollTop();
	var visibleBottom = containerTop + container.innerHeight();

	tags.each(function (index, element) {
		var rect = element.getBoundingClientRect();
		var containerRect = container.get(0).getBoundingClientRect();
		var top = (rect.top - containerRect.top) + containerTop;

		if (top < visibleBottom + 60) {
			var form = $(element).data("form");
			var fd;
			if (form === undefined) {
				fd = new FormData();
			} else {
				var formTag = parent_obj.find("#" + form).get(0);
				fd = formTag ? new FormData(formTag) : new FormData();
			}

			var datalist = $(this).data();
			for (key in datalist) {
				fd.append(key, datalist[key]);
			}

			element.remove();
			if (dialog_id == "#work_area_second") {
				second_work_area_scroll_restore_top = container.scrollTop();
			}
			setTimeout(function () {
				appcon("app.php", fd);
			}, 1);
		}
	});
}

function bind_search_box_auto_submit(dialog_id) {
	var root = (dialog_id && dialog_id !== "") ? $(dialog_id) : $("body");
	if (root.length === 0) {
		root = $("body");
	}

	root.find(".search_box").each(function () {
		var box = $(this);
		var form = box.find("form.search_form_flex").first();
		var button = box.find(".search_right button.ajax-link").first();
		if (form.length === 0 || button.length === 0) {
			return;
		}

		form.off(".auto_search");
		var timer = null;
		var triggerSearch = function (delayMs) {
			if (timer) {
				clearTimeout(timer);
				timer = null;
			}
			var execute = function () {
				if (button.prop("disabled")) {
					return;
				}
				button.trigger("click");
			};
			if (delayMs > 0) {
				timer = setTimeout(execute, delayMs);
			} else {
				execute();
			}
		};

		form.on("change.auto_search", "input,select,textarea", function () {
			var tag = (this.tagName || "").toLowerCase();
			var type = (this.type || "").toLowerCase();
			if ((tag === "input" && (type === "text" || type === "search" || type === "number")) || tag === "textarea") {
				triggerSearch(350);
				return;
			}
			triggerSearch(0);
		});

		form.on("input.auto_search", "input[type='text'],input[type='search'],input[type='number'],textarea", function () {
			triggerSearch(350);
		});
	});
}

// デバッグ画面は使わない
$("#show_debug").hide();
$("#debug_window").hide();


function append_debug_window(msg, data = "", flg_type = "") {

	if (msg === undefined) {
		return;
	}

	if (flg_type != "error") {
		if ($("#testserver").html() != "true") {
			return;
		}
	}

	if (flg_type == "table") {
		var color_key = "color:#4BA3FF;";
		var color_sep = "color:black;";
		var color_val = "color:#FCAF3E;";
		var color_title = "color:#4E9A06;font-weight:bold;"
		console.log("%c" + msg, color_title);
		for (let key in data) {
			if (key == "class" || key == "function") {
				console.log("%c" + key + '%c: %c' + data[key], color_key + "font-weight:bold;", color_sep, color_val + "font-weight:bold;");
			} else if (key == "slicedata") {
				console.log("%c" + key + '%c: %c' + "-base64-", color_key, color_sep, color_val);
			} else {
				console.log("%c" + key + '%c: %c' + data[key], color_key, color_sep, color_val);
			}

		}
		return;
	}

	if (flg_type == "error") {
		console.error(msg);
		if (data != "") {
			console.error(data);
		}
	} else {
		console.log(msg);
		if (data != "") {
			console.log(data);
		}
}
}



function set_windowID() {

	var windowID;

	// Set new window ID
	var new_windowID = $("#new_windowID").html();
	if (new_windowID != "") {
		sessionStorage.setItem('windowID', new_windowID);
		$("#new_windowID").html("");
		windowID = new_windowID;
	} else {
		windowID = sessionStorage.getItem('windowID');
		if (!windowID) {

			// 基本的にここには入らないはず！
			// 下記は万一の場合のコード

			// New tab
			windowID = "WID_" + Date.now().toString();  // 現在の時刻をミリ秒で取得して文字列に変換
			sessionStorage.setItem('windowID', windowID);

			// Old windowID
			var old_windowID = Cookies.get("windowID");
			Cookies.set("old_windowID", old_windowID, cookieOpt());
		}
	}
	// CookieにwindowIDをセット
	Cookies.set("windowID", windowID, cookieOpt());
}

$(window).on('focus', function () {
	set_windowID();
});
set_windowID();


//---------------------
// 多言語対応
//---------------------
var lang_list = {};   // lang_list[英語][種類(en/jp)]
function get_lang_list() {

	//初期化
	lang_list = {};

	//サーバからリストをとってくる
	var fd = new FormData();
	fd.append("class", "lang");
	fd.append("function", "list");
	var url = "app.php";
	appcon(url, fd, function (data) {
		if (data !== undefined) {
			lang_list = data["list"];

			translate();
		} else {
			//ログイン画面
			lang_list["base"] = {
				"Login ID":
						{
							en: "Login ID",
							jp: "ログインID"
						},
				"Password":
						{
							en: "Password",
							jp: "パスワード"
						},
				"English":
						{
							en: "English",
							jp: "英語"
						},
				"Japanese":
						{
							en: "Japanese",
							jp: "日本語"
						},
				"Login":
						{
							en: "Login",
							jp: "ログイン"
						},
			}
			translate();
		}
	});
}

function translate() {

	// // 事前準備
	$(".lang").each(function (index) {
		//タグ内に英語が設定されていない場合は lang_en に保持しておく
		var en;
		let lang_prop = '';
		en = $(this).html();
		en = en.trim();

		if ($(this).attr("lang_en") === undefined) {

			if ($(this).prop("tagName") == "SELECT") {
				$(this).removeClass("lang");
				$(this).find("option").each(function (index) {
					en = $(this).html();
					$(this).addClass("lang");
					$(this).attr("lang_prop", "html");
					$(this).attr("lang_en", en)
				});
			}

			if ($(this).prop("tagName") == "INPUT" && $(this).attr("type") == "radio") {

				var parentlabel = $(this).parent("label");
				en = parentlabel.text();
				var inputtag = parentlabel.find("input");
				parentlabel.html("");
				parentlabel.append(inputtag);
				parentlabel.append('<span>');
				var spantag = parentlabel.find("span");
				spantag.addClass("lang");
				spantag.attr("lang_prop", "html");
				spantag.attr("lang_en", en);
				spantag.html(en);
				parentlabel.wrapInner("<span>");
				$(this).removeClass("lang");

			} else {
				if (en == '') {
					en = $(this).val();
					if (en == '') {
						en = $(this).attr('placeholder');
						if (en != '') {
							lang_prop = 'placeholder';
						}
					} else {
						lang_prop = 'val';
					}
				} else {
					lang_prop = 'html';
				}
				$(this).attr("lang_en", en);
				$(this).attr("lang_prop", lang_prop);
			}

		}
	});


	//翻訳実行
	var selected_lang = "jp";
	$(".lang").each(function (index) {

		var en = $(this).attr("lang_en");

		//classnameを取得
		var parent = $(this).parents(".lang_check_area");
		var classname = "";
		if (parent.length > 0) {
			// Multi Windowの場合
			classname = parent.data("classname");
		}

		// 言語セレクターに合わせて変更
		if (lang_list[classname] === undefined || lang_list[classname][en] === undefined) {
			// Nothing to do
		} else {

			// 翻訳する
			var d = lang_list[classname][en];
			var transrated = d[selected_lang];

			if (transrated != "") {
				let lang_prop = $(this).attr("lang_prop");
				if (lang_prop == 'html') {
					$(this).html(transrated);
				} else if (lang_prop == 'val') {
					$(this).val(transrated);
				} else if (lang_prop == 'placeholder') {
					$(this).attr('placeholder', transrated);
				}

			}
		}
	});


}

$(function () {
	get_lang_list();
	Cookies.set("lang", "jp", cookieOpt());

	if ($("#testserver").html() == "true") {

		// 古いcheck_langを削除
		$(".check_lang").remove();

		// check_langを左画面下に追加
		$("BODY").append('<div class="check_lang">Edit Translation of this page</div>');

	}

	translate();

	// 翻訳の編集画面表示
	// 画面から .langを検索して、リストをサーバーに送付
	$("body").on("click", ".check_lang", function (e) {

		var data = [];
		$("body").find(".lang").each(function () {

			var en = $(this).attr("lang_en");

			if ($(this).attr("lang_en") === undefined) {
				en = $(this).html();
				en = en.trim();
			}
			var parent = $(this).parents(".lang_check_area");
			if (parent.length !== 0) {
				data.push({
					"classname": parent.data("classname"),
					"en": en,
				});
			}
		});

		var fd = new FormData();
		fd.append("class", "lang");
		fd.append("function", "open_edit_dialog");
		fd.append("data", JSON.stringify(data));
		appcon("app.php", fd);

	});
});





//-----------------------------------
// マルチダイアログのデフォルト関数
// execute when opening a new window
//-----------------------------------
append_function_dialog("__all__", function (dialog_id, flg_window = false) {

	var selected_lang = "jp";

	// World_date_time
	exec_world_datetime();


	// Datepicker
	$(dialog_id + " .datepicker").on("click", function () {

		if ($(this).hasClass("hasDatepicker")) {
			return;
		}

		$(this).prop('readOnly', true);
		var width = $(this).width();
		$(this).wrap('<div class="datepicker_area" style="width' + width + 'px;display:block;">');
		$(this).after('<div class="datepicker_clear">x</div>');
		$(this).parent().find(".datepicker_clear").on("click", function () {
			$(this).parent().find(".datepicker").val("");
			$(this).parent().find(".datepicker").trigger("change");
		});
		if (selected_lang == "jp") {
			$.datepicker.setDefaults({
				closeText: "閉じる",
				prevText: "&#x3C;前",
				nextText: "次&#x3E;",
				currentText: "今日",
				monthNames: ["1月", "2月", "3月", "4月", "5月", "6月",
					"7月", "8月", "9月", "10月", "11月", "12月"],
				monthNamesShort: ["1月", "2月", "3月", "4月", "5月", "6月",
					"7月", "8月", "9月", "10月", "11月", "12月"],
				dayNames: ["日曜日", "月曜日", "火曜日", "水曜日", "木曜日", "金曜日", "土曜日"],
				dayNamesShort: ["日", "月", "火", "水", "木", "金", "土"],
				dayNamesMin: ["日", "月", "火", "水", "木", "金", "土"],
				weekHeader: "週",
				dateFormat: "yy/mm/dd",
				firstDay: 0,
				isRTL: false,
				showMonthAfterYear: true,
				yearSuffix: "年"
			});
		} else {
			$.datepicker.setDefaults({
				closeText: "Close",
				prevText: "&#x3C;Prev",
				nextText: "Next&#x3E;",
				currentText: "Today",
				monthNames: ["January", "February", "March", "April", "May", "June",
					"July", "August", "Septempber", "October", "November", "December"],
				monthNamesShort: ["Jan", "Feb", "Mar", "Apl", "May", "June",
					"July", "Aug", "Sept", "Oct", "Nov", "Dec"],
				dayNames: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
				dayNamesShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
				dayNamesMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
				weekHeader: "Week",
				dateFormat: "yy/mm/dd",
				firstDay: 0,
				isRTL: false,
				showMonthAfterYear: false,
				yearSuffix: ""
			});
		}
		var dp_txt = $(this);
		$(this).datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange: "1930:+30",
			beforeShow: function (input, inst) {
				var cal = inst.dpDiv;
				var top = dp_txt.offset().top - $(window).scrollTop() + dp_txt.outerHeight();
				var left = dp_txt.offset().left;

				if (top + 300 > $(window).innerHeight()) {
					top = top - dp_txt.outerHeight() - 300;
				}

				setTimeout(function () {
					cal.css({
						'top': top,
						'left': left
					});
				}, 10);
			}
		});
		var c = $(this);
		setTimeout(function () {
			c.datepicker("show");
		}, 1);
	});

	// Timepicker
	var timepicker_change = true;
	// 二重送信を防ぐ
	$(dialog_id + ' .timepicker').on("change", function () {
		timepicker_change = false;
		setTimeout(function () {
			timepicker_change = true;
		}, 1000);
	});
	$(dialog_id + ' .timepicker').each(function () {
		var t = $(this).val();
		$(this).timepicker({
			timeFormat: 'H:mm',
			interval: 60,
			minTime: '0',
			maxTime: '23',
			defaultTime: t,
			dynamic: false,
			dropdown: true,
			scrollbar: false,
			zindex: 99999999999999,
			change: function (time) {
				// 多重送信を防ぐ
				if (timepicker_change) {
					timepicker_change = false;
					$(this).change();
					setTimeout(function () {
						timepicker_change = true;
					}, 1000);
				} else {
					return;
				}
			}
		});
	});

	// YearMonth picker
	$('.year_month_picker').year_month_picker();

	// RADIO
	$(".checkboxradio").checkboxradio({
		icon: false
	});

	// CHECKBOX (fr_checkbox)
	$(dialog_id + ' .fr_checkbox').each(function () {
		var input = $(this).find("input");
		var unchecked = $(this).find(".unchecked");
		var checked = $(this).find(".checked");
		var unselected = $(this).find(".unselected");

		if ($(this).data("search") == "1") {
			input.hide();

			var fr_checkbox_select = function (i, unselected, unchecked, checked) {
				unselected.removeClass("on");
				unchecked.removeClass("on");
				checked.removeClass("on");
				if (i == "0") {
					unchecked.addClass("on");
				} else if (i == "1") {
					checked.addClass("on");
				} else {
					unselected.addClass("on");
				}
			}

			fr_checkbox_select(input.val(), unselected, unchecked, checked);

			unchecked.on("click", function (e) {
				input.val(0);
				fr_checkbox_select(0, unselected, unchecked, checked);
			});

			checked.on("click", function (e) {
				input.val(1);
				fr_checkbox_select(1, unselected, unchecked, checked);
			});

			unselected.on("click", function (e) {
				input.val("");
				fr_checkbox_select("", unselected, unchecked, checked);
			});


		} else {
			if (input.val() == 1) {
				unchecked.hide();
			} else {
				checked.hide();
			}
			input.hide();

			unchecked.on("click", function (e) {
				input.val(1);
				unchecked.hide();
				checked.show();
			});

			checked.on("click", function (e) {
				input.val(0);
				unchecked.show();
				checked.hide();
			});
		}

	});


	// Autocomplate Off
	$(dialog_id + ' input').prop("autocomplete", "off");

	// Search box auto-submit
	bind_search_box_auto_submit(dialog_id);

	// Scroll Event
	if (flg_window == false) {
		$(dialog_id + " .multi_dialog_scroll").off("scroll");
		$(dialog_id + " .multi_dialog_scroll").on("scroll", function (e) {
			ajax_auto_exe(dialog_id);
		});
		// ajax-linkを一回動かす
		ajax_auto_exe(dialog_id);
	} else {
			if (dialog_id == "#work_area_second") {
				var panelBody = $("#work_area_second .work_area_second_body");
				panelBody.off("scroll.ajax_auto");
				panelBody.on("scroll.ajax_auto", function () {
					ajax_auto_exe_in_scroll_container("#work_area_second", "#work_area_second .work_area_second_body");
				});
				ajax_auto_exe_in_scroll_container("#work_area_second", "#work_area_second .work_area_second_body");
			} else {
				var tag_ajax_auto = $("body").find(".ajax-auto");
				$(window).off("scroll.ajax_auto_body");
				if (tag_ajax_auto.length > 0) {
					$(window).on("scroll.ajax_auto_body", function () {
						ajax_auto_exe("body");
					});
				}
			}
		}

	// color picker
	jQuery(function ($) {
		$('.colorpicker').asColorPicker();
	});

	// 文字バイトカウンター
	$(dialog_id + " .wordcounter").text_size_limit({"max": -1});

	// 多言語
	translate();

	// ドロップダウンに検索機能
	$(dialog_id + " select").each(function () {
		var $sel = $(this);

		// 既に select2 済みなら破棄してクリーンにする
		if ($sel.hasClass("select2-hidden-accessible")) {
			try {
				$sel.select2("destroy");
			} catch (e) {
				// destroy 失敗は無視（壊れてる時にここに来ることがある）
			}
		}

		// optionが一定数以上なら select2 を付ける
		if ($sel.children().length > 10) {
			$sel.select2({
				language: "ja",
				dropdownParent: $(document.body)
			});
		}
	});

	//Vimeo
	$(dialog_id + " .vimeo").each(function (index, element) {
		vimeo_player(this);
	});

	fr_file_upload_init();
	fr_email_verify_init();

	// Badge処理
	$(".badge").each(function (index, element) {
		var val = $(this).html();
		if (val === undefined || val == "" || val == 0) {
			$(this).hide();
		}
	});

	// geometry_location
	$(".geometry_location").on("focus", function (e) {
		$("button").each(function (index, element) {
			$(this).data("bgcolor", $(this).css("background-color"));
			$(this).css("background-color", "#CCC");
			$(this).css("pointer-events", "none");
		});
	});

	$(".geometry_location").on("focusout", function (e) {

		$('input[name="geometry_location"]').remove();
		var input_tag = document.createElement('input');
		$(input_tag).attr("name", "geometry_location");
		$(input_tag).attr("type", "hidden");
		$(this).parent().append(input_tag);

		get_geometry_location($(this), $(input_tag));

	});

	// select2のアイテムからtitle属性を削除
	$('.select2-selection__rendered').hover(function () {
		$(this).removeAttr('title');
	});

	// Vimeo thumbnail
	$(".vimeo_thumbnail").each(function () {
		if ($(this).find("img").length == 0) {
			var obj = this;
			var vimeo_id = $(this).data("vimeo_id");
			var fd = new FormData();
			fd.append("class", "_VIMEO");
			fd.append("function", "_THUMBNAIL");
			fd.append("vimeo_id", vimeo_id);
			appcon("app.php", fd, function (data) {
				var img = document.createElement('img');
				$(img).attr("src", data["url"]);
				obj.append(img);
			});
		}
	});

	set_parameters_to_button();

});
// public用に１回動かす
multi_dialog_functions["__all__"]("", true);


// ボタンに自動でajax-linkとclassnameを付与
function set_parameters_to_button() {
	// buttonタグ、.ajax-autoクラス、.ajax-linkクラスを対象に検索
	$('[invoke-function]').each(function () {

		// CHANGE: download-link は例外（何もしない）
		if ($(this).hasClass('download-link')) {
			return true; // continue
		}

		// (1) "getting_dialog_id"をclosestで検索
		var closestElement = $(this).closest('.getting_dialog_id');

		// (2) closest要素のdata-classnameの値を取得
		var dataClassName = closestElement.data('classname');

		// "invoke-function"属性が設定されている場合のみ処理を実行
		if ($(this).attr('invoke-function') !== undefined) {
			var functionValue = $(this).attr('invoke-function');
			$(this).removeAttr('invoke-function').attr('data-function', functionValue);

			if (!$(this).hasClass('ajax-auto')) {
				$(this).addClass('ajax-link');
			}

			// "data-class"属性を追加し、値を設定
			$(this).attr('data-class', dataClassName);
		}

		// "invoke-class"属性が設定されている場合
		if ($(this).attr('invoke-class') !== undefined) {
			var cvalue = $(this).attr('invoke-class');
			$(this).removeAttr('invoke-class').attr('data-class', cvalue);
		}

	});
}

function summarize_multi_dialog_state(dialog_id, dialog_name, classname) {
	var $dlg = $(dialog_id);
	if ($dlg.length === 0) {
		return null;
	}

	var buttons = [];
	$dlg.find("button:visible, a.ajax-link:visible").each(function (idx) {
		if (idx >= 50) {
			return false;
		}
		var $el = $(this);
		buttons.push({
			tag: this.tagName.toLowerCase(),
			text: String($el.text() || "").trim().slice(0, 80),
			data_class: String($el.data("class") || ""),
			data_function: String($el.data("function") || ""),
			data_form: String($el.data("form") || ""),
			id: String($el.attr("id") || ""),
			name: String($el.attr("name") || "")
		});
	});

	var forms = [];
	$dlg.find("form").each(function (idx) {
		if (idx >= 10) {
			return false;
		}
		var $form = $(this);
		var fields = [];
		$form.find("input, textarea, select").each(function (fidx) {
			if (fidx >= 80) {
				return false;
			}
			var $field = $(this);
			fields.push({
				tag: this.tagName.toLowerCase(),
				type: String($field.attr("type") || ""),
				name: String($field.attr("name") || ""),
				id: String($field.attr("id") || "")
			});
		});
		forms.push({
			id: String($form.attr("id") || ""),
			field_count: fields.length,
			fields: fields
		});
	});

	return {
		dialog_id: dialog_id,
		dialog_name: dialog_name || "",
		classname: classname || "",
		title: String(
			$dlg.find(".multi_dialog_innder_title").first().text()
			|| $dlg.find(".multi_dialog_title_area").first().clone().find(".multi_dialog_close").remove().end().text()
			|| ""
		).trim().slice(0, 120),
		button_count: buttons.length,
		form_count: forms.length,
		buttons: buttons,
		forms: forms
	};
}

// public用に１回動かす
$(function () {
	var exec_classname = $("#page_classname").data("class");
	if (exec_classname) {
		var func = multi_dialog_functions[exec_classname];
		if (func) {
			func("");
		}
	}
});

// public用に１回動かす
ajax_auto_exe("body");



$(function () {
	$("body").on("click", ".window_large", function (e) {
		var p = $(this).parents(".multi_dialog");
		console.log(p);
		//p.css("transform","scale(1,1)");
		p.css("opacity", "1");
	});
	$("body").on("click", ".window_small", function (e) {
		var p = $(this).parents(".multi_dialog");
		console.log(p);
		//p.css("transform","scale(0.4,0.4)");
		p.css("opacity", "0.5");
	});
});


$("body").on("change", "#vimeo_id", function (e) {

	var raw = $("#vimeo_id").val().trim();

	// URL → ID の変換
	// ・末尾の数字だけを抜き出す
	// ・"https://vimeo.com/123456789" → "123456789"
	var vid = raw.replace(/^https?:\/\/vimeo\.com\//, '');

	var formData = new FormData();
	formData.append("class", "upload");
	formData.append("function", "change_to_vimeo_id");
	formData.append("txt", vid);

	appcon("app.php", formData, function (d) {
		$("#vimeo_id").val(d["vimeo_id"]);
	});
});

var sliced_file_id = "";
$("body").on("change", "#sliced_file", function (e) {

	$("#sliced_error").html("Uploading...");

	var k = 0;
	var slice_size = 1024 * 100; // Buffer size

	var f = $("#sliced_file").prop('files')[0];
	if (f == null) {
		return;
	}

	var mode = $("#sliced_file").data("mode");

	var size = f.size;
	var filename = f.name;
	var count = Math.ceil(size / slice_size); // Calculate the number of slices
	sliced_file_id = 0;

	function uploadSlice() {
		if (k < count) {
			var splitData = f.slice(k * slice_size, (k + 1) * slice_size); // Slice the file
			k++;

			var reader = new FileReader();
			reader.onload = function (event) {
				var base64 = event.target.result;
				var formData = new FormData();
				formData.append("slicedata", btoa(base64));
				formData.append("filename", filename);
				formData.append("k", k);
				formData.append("class", "upload");
				formData.append("function", "sliced_data");
				formData.append("sliced_file_id", sliced_file_id)

				appcon("app.php", formData, function (d) {

					sliced_file_id = d["sliced"]["sliced_file_id"];

					$("#sliced_error").html("Uploading... " + k + " / " + count + " " + Math.ceil(100 * k / count).toString() + "% ");
					uploadSlice(); // Upload the next slice
				});
			};
			reader.readAsBinaryString(splitData);
		} else {

			if (mode == "vimeo") {
				$("#sliced_error").html("Sending the file to Vimeo server... ");
				var formData = new FormData();
				formData.append("class", "upload");
				formData.append("function", "send_to_vimeo");
				formData.append("sliced_file_id", sliced_file_id);
				formData.append("title", $("#vimeo_title").val());
				formData.append("description", $("#vimeo_description").val());
				appcon("app.php", formData, function (d) {
					if (d["vimeo"]["result"] == "success") {
						var vimeo_id = d["vimeo"]["vimeo_id"];
						$("#vimeo_id").val(vimeo_id);
						$("#sliced_error").html("Success uploading");
					} else {
						$("#sliced_error").html("Fail uploading");
					}
				});
			} else {
				$("#sliced_file_id", sliced_file_id);
				$("#sliced_error").html("Upload complete!");
			}
		}
	}

	uploadSlice(); // Start the upload process
});



//Viemo player
function vimeo_player(getobj) {

	var obj = getobj;

	var id = "vimeo_player_" + $(obj).data("vimeo_id");

	$(obj).attr("id", id);

	var vimeo_id = $(obj).data("vimeo_id");
	var options = {
		id: vimeo_id,
		responsive: true,
		autopause: true,
	};
	var player = new Vimeo.Player(id, options);
	player.setVolume(1);
	player.on('play', function () {

	});
	player.on('ended', function () {
		player.destroy();
		$(obj).html("");
		vimeo_player(obj);
	});
	player.on('pause', function () {
	});
}

// $('#left-sidebar-show-btn').click(function () {
// 	// $('#menu_area').css('display', 'block').css('width', '0px').css('width', '300px');
// 	multi_dialog_zindex++;
// 	$('#menu_area').addClass('detect_outside_click').css('left', '0').css("z-index",multi_dialog_zindex);
// });

$(document).on('click', '.left-sidebar-hide-btn, #sidemenu .ajax-link', function () {
	// $('#menu_area').css('display', 'block').css('width', '0px').css('width', '300px');
	// $('#menu_area').removeClass('detect_outside_click').css('left', '-360px');
	if (sidemenu_from == 'right') {
		var document_width = $(document).width();
		$('#sidemenu').removeClass('detect_outside_click').animate({'left': document_width + 'px'});
	} else {
		var sidemenu_width = $('#sidemenu').width();
		$('#sidemenu').removeClass('detect_outside_click').animate({'left': '-' + sidemenu_width + 'px'});
	}
});

var sidemenu_from;
$(document).mouseup(function (e) {
	if ($(e.target).closest(".detect_outside_click").length === 0) {
		$('#menu_area').removeClass('detect_outside_click').css('left', '-360px');
		$('#popup').removeClass('detect_outside_click').css('opacity', '0').css('display', 'none');

		if (sidemenu_from == 'right') {
			var document_width = $(document).width();
			$('#sidemenu').removeClass('detect_outside_click').animate({'left': document_width + 'px'}, 200, function () {
				$(this).remove();
			});
		} else {
			var sidemenu_width = $('#sidemenu').width();
			$('#sidemenu').removeClass('detect_outside_click').animate({'left': '-' + sidemenu_width + 'px'}, 200, function () {
				$(this).remove();
			});
		}
	}



	if (year_month_picker) {
		if ($(e.target).closest(".detect_outside_click").length === 0) {
			$(".year_month_picker_panel").hide();
			$(".year_month_picker_panel").removeClass("detect_outside_click");
			year_month_picker = false;
		}
	}
});






// World Date Time
function exec_world_datetime() {
	$(".world_datetime").each(function () {
		var element = $(this);

		// elementをhiddenにする
		element.hide();
		element.removeClass("world_datetime");

		// wrap_datetimeでelementを囲む
		var wrapDiv = $('<div class="wrap_datetime"></div>');
		element.after(wrapDiv);
		wrapDiv.append(element);

		// elementのvalueから日付と時間を分割して設定（ローカルタイムゾーンを考慮）
		if (element.is('input')) {
			if (element.val() > 0) {
				var datetime = new Date(element.val() * 1000);
			} else {
				var datetime = null;
			}
		} else {
			if (element.html() > 0) {
				var datetime = new Date(element.html() * 1000);
			} else {
				var datetime = null;
			}
		}

		if (datetime !== null) {
			var year = datetime.getFullYear();
			var month = ('0' + (datetime.getMonth() + 1)).slice(-2);
			var day = ('0' + datetime.getDate()).slice(-2);
			var hours = ('0' + datetime.getHours()).slice(-2);
			var minutes = ('0' + datetime.getMinutes()).slice(-2);
		}
		var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

		if (element.is('input')) {
			// テキストボックスを２つ追加する
			var tag_date = $('<input type="text" class="datepicker" placeholder="yyyy/mm/dd">');
			var tag_time = $('<input type="text" class="timepicker" placeholder="hh:mm">');
			var tag_timezone = $(`<span>${timezone}</span>`);
			wrapDiv.append(tag_date);
			wrapDiv.append(tag_time);
			wrapDiv.append(tag_timezone);
			if (datetime !== null) {
				tag_date.val(year + '/' + month + '/' + day);
				tag_time.val(hours + ':' + minutes);
			} else {
				tag_date.val("");
				tag_time.val("00:00");
			}

			// tag_date, tag_timeに変化があったら、elementのvalueを更新（UTCに変換）
			function updateElementValue() {
				var dateParts = tag_date.val().split('/');
				var timeParts = tag_time.val().split(':');

				if (dateParts.length === 3 && timeParts.length === 2) {
					// 新しい日付と時間のローカルタイムを取得
					var newDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2], timeParts[0], timeParts[1]);
					var newTimestamp = Math.floor(newDate.getTime() / 1000);
					element.val(newTimestamp);
				} else {
					element.val("");
				}
			}

			tag_date.on('change', updateElementValue);
			tag_time.on('change', updateElementValue);
		} else {
			if (datetime !== null) {
				var tag_p = $(`<div class="datetime_timestamp">${year}/${month}/${day} ${hours}:${minutes} <div class="datetime_timezone">(${timezone})</div></div>`);
			} else {
				var tag_p = "";
			}
			wrapDiv.append(tag_p);
		}

	});
}

//--------------------
// file uploader with paste and drag & drop support
//--------------------

var fr_file_upload_active_div_id = '';
var fr_file_upload_active_div_number = 0;
function fr_file_upload_init() {
	var fr_file_upload_count = 0;
	$('.fr_image_paste').each(function () {
		let input_name = $(this).attr('name');
		let text = $(this).data('text');
		if (text === undefined || text == '') {
			text = "File Upload";
		}
		let divStyle = $(this).data('div_style');

		let multiple = $(this).data('multiple');
		let maxLength = $(this).data('max_length');

		fr_file_upload_count++;
		let file_upload_html;

		if (multiple === true) {
			file_upload_html = `<div class="fr_file_upload_div" id="fr_file_upload_div_${fr_file_upload_count}" data-number="${fr_file_upload_count}" style="${divStyle}">
			<div class="fr_file_upload_input_div upload__box">
				<div>
					<input type="file" name="${input_name}" multiple="" data-multiple="true" data-max_length="${maxLength}" class="fr_file_input upload_multiple_img" id="fr_file_input_${fr_file_upload_count}">
					<p class="lang" style="margin: 5px 0 0 0; font-size: 12px;">Drag & drop here</p>
				</div>
				<div class="fr_file_preview_div"></div>
                                <div class="upload__img-wrap"></div>
			</div>
		</div>`;
		} else {
			file_upload_html = `<div class="fr_file_upload_div" id="fr_file_upload_div_${fr_file_upload_count}" data-number="${fr_file_upload_count}" style="${divStyle}">
			<div class="fr_file_upload_input_div">
				<div>
					<input type="file" name="${input_name}" class="fr_file_input" id="fr_file_input_${fr_file_upload_count}">
					<p class="lang" style="margin: 5px 0 0 0; font-size: 12px;">Drag & drop here</p>
				</div>
				<div class="fr_file_preview_div"></div>
			</div>
		</div>`;
		}
		imgArray = [];
		$(this).after(file_upload_html);
		$(this).attr('data-name', input_name);
		$(this).attr('data-id', fr_file_upload_count);
		$(this).attr('name', '');
		$(this).attr('id', 'fr_old_file_name_' + fr_file_upload_count);
		$(this).addClass('hidden');
		$(this).removeClass('fr_image_paste');
	});
}
fr_file_upload_init();

var imgWrap = "";
var imgArray = [];
$(document).on('change', '.fr_file_input', function (e) {

	if (!e.target.files.length)
		return;
	var ismultiple = $(this).data('multiple');
	$(this).closest('.fr_file_upload_input_div').children('.fr_file_preview_div').html('');
	if (!ismultiple) {
		if (e.target.files[0].type.startsWith('image/')) {
			const img = document.createElement('img');
			const blob = URL.createObjectURL(e.target.files[0]);
			img.src = blob;
			img.style.width = "100px";
			$(this).closest('.fr_file_upload_input_div').children('.fr_file_preview_div').html(img);
		}
	} else {

		var max_length = $('.upload_multiple_img').data('max_length');
		var imgfiles = e.target.files;
		fr_multiple_img_add(max_length, imgfiles);

	}
});

$('body').on('click', ".upload__img-close", function (e) {
	var file = $(this).parent().data("file");
	for (var i = 0; i < imgArray.length; i++) {
		if (imgArray[i].name === file) {
			imgArray.splice(i, 1);
			break;
		}
	}
	let list = new DataTransfer();
	imgArray.forEach(function (f, index) {
		list.items.add(f);
	});
	let myFileList = list.files;
	document.getElementById("fr_file_input_" + fr_file_upload_active_div_number).files = myFileList;
	$(this).parent().parent().remove();
});

$(document).on('click', '.fr_file_upload_div', function () {
	fr_file_upload_active_div_id = this.id;
	fr_file_upload_active_div_number = $(this).data('number');
	//$('.fr_file_upload_div').css('border', 'solid 1px black');
	//$(this).css('border', 'solid 2px blue');
});



$(document)
		.on('dragover', '.fr_file_upload_div', function (e) {
			$(this).addClass('fr_file_upload_draggin');
			return false;
		}).on('dragleave', '.fr_file_upload_div', function (e) {
	fr_dragging = true;
	$(this).removeClass('fr_file_upload_draggin');
	return false;
}).on('drop', '.fr_file_upload_div', function (e) {
	fr_file_upload_active_div_id = this.id;
	fr_file_upload_active_div_number = $(this).data('number');
	$('#' + fr_file_upload_active_div_id).find('.fr_file_preview_div').html('');
	document.getElementById("fr_file_input_" + fr_file_upload_active_div_number).files = e.originalEvent.dataTransfer.files;
	var ismultiple = $(this).find('.fr_file_input').data('multiple');
	if (!ismultiple) {
		//create image
		if (e.originalEvent.dataTransfer.files[0].type.startsWith('image/')) {
			const img = document.createElement('img');
			const blob = URL.createObjectURL(e.originalEvent.dataTransfer.files[0]);
			img.src = blob;
			img.style.width = "100px";
			$('#' + fr_file_upload_active_div_id).find('.fr_file_preview_div').html(img);
		}
	} else {
		var max_length = $('.upload_multiple_img').data('max_length');
		var imgfiles = e.originalEvent.dataTransfer.files;
		fr_multiple_img_add(max_length, imgfiles);
	}
	$(this).children('.fr_file_upload_btn_div').addClass('hidden');
	$(this).children('.fr_file_upload_input_div').removeClass('hidden');
	return false;
});
// end - file uploader
function fr_multiple_img_add(max_length, imgfiles) {
	$('.upload_multiple_img').each(function () {
		//$(this).on('change', function (e) {
		imgWrap = $(this).closest('.upload__box').find('.upload__img-wrap');
		var maxLength = max_length;

		//var files = e.target.files;
		var files = imgfiles;
		var filesArr = Array.prototype.slice.call(files);
		var iterator = 0;
		let list = new DataTransfer();

		filesArr = imgArray.concat(filesArr);
		filesArr = filesArr.filter((item, pos) => filesArr.indexOf(item) === pos);

		if (filesArr.length > maxLength) {
			alert('You can not select more than ' + maxLength + ' files');
			//return false;
		}
		var len = 0;
		imgWrap.html('');
		imgArray = [];
		filesArr.forEach(function (f, index) {

			len++;
			if (len <= maxLength) {

				list.items.add(f);
				imgArray.push(f);

				var reader = new FileReader();
				reader.onload = function (e) {
					var html = "<div class='upload__img-box'><div style='background-image: url(" + e.target.result + ")' data-number='" + $(".upload__img-close").length + "' data-file='" + f.name + "' class='img-bg'><div class='upload__img-close'></div></div></div>";
					if (!f.type.match('image.*')) {
						html = "<div class='upload__img-box'><div class='img-bg' style=''><p class='upload__noimage'>" + f.name + "</p><div class='upload__img-close'></div></div></div>";
					}

					imgWrap.append(html);
					iterator++;
				}
				reader.readAsDataURL(f);
			}
		});
		let myFileList = list.files;
		document.getElementById("fr_file_input_" + fr_file_upload_active_div_number).files = myFileList;
	});
}


// ----------------------------
// Email verification component
// ----------------------------
var fr_email_verify_count = 0;
var fr_email_verify_active_div_id = '';
var fr_email_verify_active_div_number = 0;
var fr_email_verify_btn_enter = false;
function fr_email_verify_init() {
	$('.fr_verification_mail').each(function () {
		let input_name = $(this).attr('name');

		fr_email_verify_count++;
		let file_upload_html = `<div class="fr_email_veriry_main_div hidden" id="fr_email_verify_main_div_${fr_email_verify_count}" data-number="${fr_email_verify_count}">
			<input type="hidden" name="${input_name}" class="fr_email_veriry_hidden_field" />
			<div class="fr_email_verify_first_div ">
				<div style="display:flex;">
					<span class="ui-icon ui-icon-triangle-1-w fr_email_veriry_email_back_btn" style="transform: scale(2); margin-top:0px;"></span>
					<p class="fr_email_veriry_email_p" style="margin: 0 10px"></p>
				</div>
				<button class="fr_email_verify_send_btn" data-class="user" data-function="fr_verification_mail_send" data-email="" data-key="">Send Verify Mail</button>
			</div>
			<div class="fr_email_verify_second_div hidden">
				<div style="display:flex;">
					<input class="fr_email_verify_text" type="text" style="text-align: center;" />
				</div>
				<button class="fr_email_verify_btn" data-class="user" data-function="fr_verification_mail_send" data-email="" data-key="">Submit</button>
				<p class="fr_email_verify_error_msg hidden">Verification Faild!</p>
			</div>
			<div class="fr_email_verify_third_div hidden">
				<div style="display:flex;">
					<span class="ui-icon ui-icon-circle-check" style="transform: scale(1.5); margin:auto; cursor: pointer !important;"></span>
					<p class="fr_email_veriry_email_p" style="margin: 0 10px"></p>
				</div>
			</div>
		</div>`;
		$(this).after(file_upload_html);
		$(this).attr('data-name', input_name);
		$(this).attr('data-id', fr_email_verify_count);
		$(this).attr('name', '');
		$(this).attr('id', 'fr_email_verify_old_input_' + fr_email_verify_count);
		$(this).addClass('fr_verification_mail_new');
		$(this).removeClass('fr_verification_mail');
	});
}
fr_email_verify_init();

$(document).on('change', '.fr_verification_mail_new', function (e) {
	e.preventDefault();
	let val = $(this).val();
	let main_div_id = '#fr_email_verify_main_div_' + $(this).data('id');
	$(main_div_id).removeClass('hidden');
	$(main_div_id).find('.fr_email_veriry_email_p').html(val);
	$(this).addClass('hidden');
	$(main_div_id).attr('data-email', val);
	$(main_div_id).find('.fr_email_verify_send_btn').attr('data-email', val);
	$(main_div_id).find('.fr_email_verify_btn').attr('data-email', val);
	$('.fr_email_veriry_email_back_btn').css('cursor', 'pointer');
	$(this).val('');
});

$(document).on("keydown", '.fr_verification_mail_new', function (e) {
	if (e.which == 13) {
		fr_email_verify_btn_enter = true;
	}
	setTimeout(() => {
		fr_email_verify_btn_enter = false;
	}, 100);
});

$(document).on('click', '.fr_email_verify_send_btn', function (e) {
	e.preventDefault();
	if (!fr_email_verify_btn_enter) {
		let main_div = $(this).closest('.fr_email_veriry_main_div');
		main_div.children('.fr_email_verify_second_div').removeClass('hidden');
		$(this).parent('.fr_email_verify_first_div').addClass('hidden');
		main_div.find('.fr_email_verify_text').focus();
		let email = $(this).attr('data-email');
		let fd = new FormData();
		fd.append('class', 'user');
		fd.append('function', 'fr_verification_mail_send');
		fd.append('email', email);
		appcon('app.php', fd, function (data) {
			main_div.find('.fr_email_verify_btn').attr('data-key', data.key);
		});
	}
});

$(document).on('click', '.fr_email_verify_btn', function (e) {
	e.preventDefault();
	let main_div = $(this).closest('.fr_email_veriry_main_div');
	let key = $(this).attr('data-key');
	let code = main_div.find('.fr_email_verify_text').val();
	let email = $(this).attr('data-email');
	let fd = new FormData();
	fd.append('class', 'user');
	fd.append('function', 'fr_verification_mail_verify');
	fd.append('key', key);
	fd.append('code', code);
	appcon('app.php', fd, function (data) {
		if (data.status) {
			main_div.children('.fr_email_verify_third_div').removeClass('hidden');
			main_div.find('.fr_email_verify_second_div').addClass('hidden');
			main_div.find('.fr_email_veriry_hidden_field').val(email);
		} else {
			main_div.find('.fr_email_verify_error_msg').removeClass('hidden');
		}
	});
});

$(document).on('click', '.fr_email_veriry_email_back_btn', function (e) {
	let parent = $(this).closest('.fr_email_veriry_main_div');
	let email = parent.attr('data-email');
	let input_id = '#fr_email_verify_old_input_' + $(this).closest('.fr_email_veriry_main_div').data('number');
	$(input_id).removeClass('hidden');
	$(input_id).val(email);
	parent.addClass('hidden');
});

$(document).on('click', '.fr_email_veriry_main_div', function () {
	fr_email_verify_active_div_id = this.id;
	fr_email_verify_active_div_number = $(this).data('number');
	$('.fr_email_veriry_main_div').css('border', 'solid 1px black');
	$(this).css('border', 'solid 2px blue');
});
//end - email verification componentn

function get_geometry_location(address_tag, textbox) {
	address_tag.css("transition", "0.4s");
	var atwidth = address_tag.width() + 20;
	var address = address_tag.val();
	var color = address_tag.css("color");
	geocoder = new google.maps.Geocoder();
	geocoder.geocode({
		'address': address
	}, function (results, status) {
		if (status === google.maps.GeocoderStatus.OK) {
			textbox.val(results[0].geometry.location);

			$('.glocation-error').remove();
			$('.glocation-success').remove();
			var locnotify = $("<p class='glocation-success' style='width:" + atwidth + "px;'>Successed to get geometry location!</p>").insertAfter(address_tag);

			setTimeout(function () {
				locnotify.css("display", "none");
			}, 2000);
		} else {
			console.log("Fail to get geometry.location");
			$('.glocation-error').remove();
			$('.glocation-success').remove();

			var locnotify = $("<p class='glocation-error' style='width:" + atwidth + "px;'>Failed to get geometry.location</p>").insertAfter(address_tag);

			setTimeout(function () {
				locnotify.css("display", "none");
			}, 2000);
		}
		$("button").each(function (index, element) {
			$(this).css("background-color", $(this).data("bgcolor"));
			$(this).css("pointer-events", "auto");
		});
	});
}

var map;
var marker = [];
var infoWindow = [];
function draw_google_map(tag_id, lat, lng, zoom, markerData) {

	if (status_map == 1) {
		draw_google_map_exe(tag_id, lat, lng, zoom, markerData);
	} else {
		var map_interval_pointer = function () {
			if (status_map == 1) {
				clearInterval(map_interval_pointer);
				draw_google_map_exe(tag_id, lat, lng, zoom, markerData);
			}
		}
		setInterval(map_interval_pointer, 1000);

	}
}

function draw_google_map_exe(tag_id, lat, lng, zoom, markerData) {

	if (!$("#" + tag_id).length) {
		console.log("There is no tag ID=" + tag_id + ".");
		return;
	}

	// size check and set
	var width = $("#" + tag_id).height();
	var height = $("#" + tag_id).width();

	if (width == 0 || height == 0) {
		$("#" + tag_id).width(500);
		$("#" + tag_id).height(500);
	}

	// 地図の作成
	map = new google.maps.Map(document.getElementById(tag_id), {// #sampleに地図を埋め込む
		center: {// 地図の中心を指定
			lat: lat, // 緯度
			lng: lng // 経度
		},
		zoom: zoom  // 地図のズームを指定
	});

	var bounds = new google.maps.LatLngBounds();

	// マーカー毎の処理
	if (markerData !== undefined) {
		for (var i = 0; i < markerData.length; i++) {

			var loc = markerData[i]["location"];

			var markerLatLng = new google.maps.LatLng({lat: loc["lat"], lng: loc["lng"]}); // 緯度経度のデータ作成
			marker[i] = new google.maps.Marker({// マーカーの追加
				position: markerLatLng, // マーカーを立てる位置を指定
				map: map // マーカーを立てる地図を指定
			});

			bounds.extend(markerLatLng);

			//吹き出しデータの作成
			infoWindow[i] = new google.maps.InfoWindow({// 吹き出しの追加
				content: '<div class="map_info_window">' + markerData[i]['html'] + '</div>' // 吹き出しに表示する内容
			});

			markerEvent(i); // マーカーにクリックイベントを追加

		}

		if (zoom == 0)
			map.fitBounds(bounds);

	}

	//init autocomplete
	$(".geometry_location").each(function (index, element) {
		// var locinput = document.getElementsByClassName('geometry_location');
		var locinput = $(this)[0];
		var autocomplete = new google.maps.places.Autocomplete(locinput);
		autocomplete.addListener('place_changed', function () {
			var place = autocomplete.getPlace();
			// locinput.value = JSON.stringify(place.address_components);
			$(".geometry_location").focusout();
		});

	});

}

// マーカーにクリックイベントを追加
function markerEvent(i) {
	marker[i].addListener('click', function () { // マーカーをクリックしたとき
		infoWindow[i].open(map, marker[i]); // 吹き出しの表示
	});
}

// first appcon for DISPLAY
$(function () {
	var fd = new FormData();
	fd.append("class", "_DISPLAY");
	fd.append("function", "_ARR");
	appcon("app.php", fd, function (data) {
	});
});

function getMaxZIndex() {
	let maxZIndex = 0;
	const elements = document.querySelectorAll('*');

	elements.forEach((element) => {
		const zIndex = window.getComputedStyle(element).getPropertyValue('z-index');
		if (!isNaN(zIndex) && zIndex !== 'auto') {
			maxZIndex = Math.max(maxZIndex, parseInt(zIndex, 10));
		}
	});

	return maxZIndex;
}


var polling_status = false;
var polling_request = null;
var polling_id = null;
var polling_startTime;
function startLongPolling(md, force = false) {

	//２重起動防止
	if (force === false) {
		if (polling_status !== false) {

			//一度停止する
			stopLongPolling();
			finalizePolling(null, null);

		}
	}

	var new_polling_id = md["polling_id"];
	var nickname = md["nickname"];
	var timeout_seconds = md["timeout_seconds"];
	var timeout_handler_function = md["timeout_handler_function"];
	var timeout_handler_class = md["timeout_handler_class"];

	if (new_polling_id === null) {
		return;
	}

	if (force === false) {
		// 最初の処理
		append_debug_window("Start Polling : " + new_polling_id + " " + nickname);
		polling_startTime = Date.now(); // 現在時刻をミリ秒で取得

		// タイムアウト検出
		let intervalId = setInterval(() => {
			let elapsedSeconds = (Date.now() - polling_startTime) / 1000;

			if (elapsedSeconds > timeout_seconds) {

				if (document.visibilityState !== "visible") {
					// 画面表示中はタイムアウトしない
					clearInterval(intervalId); // インターバルを停止
					stopLongPolling();
					finalizePolling(timeout_handler_class, timeout_handler_function);
				}
			}
		}, 1000); // 10msごとにチェック
	} else {
		// ループ２回目以降の処理
		append_debug_window("Polling : " + new_polling_id + " " + nickname);
	}

	polling_status = true;
	polling_id = new_polling_id;

	polling_request = $.ajax({
		url: "polling.php", // PHPスクリプトのURL
		method: "POST", // POSTメソッドに変更
		data: md,
		success: function (response) {
			var flg_continue = true;
			try {
				var response_json = JSON.parse(response);
				var data = response_json.data;
				if (response_json.success) {
					var fd = new FormData();
					append_debug_window("Polling receives the message following:");
					Object.entries(data).forEach(([key, value]) => {
						console.log("key=" + key + " value=" + value);
						fd.append(key, value);
					});
					appcon("app.php", fd);
				} else {
					if (response_json.message === "Abort") {
						flg_continue = false;
						finalizePolling(timeout_handler_class, timeout_handler_function);

					} else if (response_json.message === "Timeout") {
						// Nothing

					} else {
						append_debug_window("Fail", response);
					}
				}
			} catch (e) {
				console.error("Invalid JSON: ", e);
			}

			// 再びポーリング開始
			if (flg_continue) {
				startLongPolling(md, true);
			}
		},
		error: function (error) {
			append_debug_window("Polling connection has the error.", error);
			if (polling_status) {
				setTimeout(function () {
					startLongPolling(md, true);
				}, 3000); // 3秒後に再接続
			}
		}
	});
}

// サーバーにポーリング停止情報を送る
function stopLongPolling() {
	navigator.sendBeacon("polling_stop.php", JSON.stringify({polling_id: polling_id}));
}

// ポーリングを安全に終了させる
function finalizePolling(timeout_handler_class, timeout_handler_function) {
	polling_status = false;
	if (polling_request !== null) {
		polling_request.abort();  // polling_status = falseなので、errorが発生しても再接続を行わない
	}
	polling_request = null;
	polling_id = null;

	if (timeout_handler_function !== null) {
		var fd = new FormData();
		fd.append("class", timeout_handler_class);
		fd.append("function", timeout_handler_function);
		appcon("app.php", fd)
	}

	// console log
	append_debug_window("Polling stoped.");
}

// ポーリングのタイムアウトを延長させる
$(document).on('mousemove keydown click touchstart', function () {
	polling_startTime = Date.now(); //タイムアウトを更新
});

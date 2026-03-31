
<div id="appcode" style="display: none;">{$appcode}</div>
{if $testserver}
	<div id="testserver" style="display: none;">true</div>
{else}
	<div id="testserver" style="display: none;">false</div>
{/if}

<div id="display_errors" style="display: none;">{$setting.display_errors}</div>

<div id="dialog"></div>
<div id="multi_dialog"></div>
<div id="new_windowID">{$new_windowID}</div>

<div id="download_view">
	<div id="download_bar">
		<div id="download_message" style="margin-left:10px;"></div>
		<div id="download_progress"></div>
	</div>
</div>

<div id="lang_priority" style="display:none;">1</div>
<div id="lang_default" style="display:none;">{$legacy_lang_default}</div>

{include file="{$base_template_dir}/scripts.tpl"}

<script>
	// bodyタグに、class="getting_dialog_id" と data-class を付与する
	$("body").addClass("getting_dialog_id");
	$("body").attr("data-classname","{$class}");
</script>

<script src="js/function.js?{$timestamp}"></script>

<script src="appjs.php?class={$class}&{$timestamp}"></script>

<div id="page_classname" data-class="{$class}" style="display: none;"></div>

<script>
	(function() {
		const body = document.body;
		const menuToggle = document.querySelector("[data-publicsite-menu-toggle]");
		const menuPanel = document.querySelector("[data-publicsite-menu]");
		const menuBackdrop = document.querySelector("[data-publicsite-menu-backdrop]");
		if (!body || !menuToggle || !menuPanel || !menuBackdrop) {
			return;
		}

		const closeMenu = function() {
			body.classList.remove("publicsite-menu-open");
			menuToggle.setAttribute("aria-expanded", "false");
			menuPanel.setAttribute("aria-hidden", "true");
		};

		const openMenu = function() {
			body.classList.add("publicsite-menu-open");
			menuToggle.setAttribute("aria-expanded", "true");
			menuPanel.setAttribute("aria-hidden", "false");
		};

		menuToggle.addEventListener("click", function() {
			if (body.classList.contains("publicsite-menu-open")) {
				closeMenu();
				return;
			}
			openMenu();
		});

		menuBackdrop.addEventListener("click", closeMenu);
		menuPanel.querySelectorAll("a").forEach(function(link) {
			link.addEventListener("click", closeMenu);
		});

		document.addEventListener("keydown", function(event) {
			if (event.key === "Escape") {
				closeMenu();
			}
		});
	})();
</script>

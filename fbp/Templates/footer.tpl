

	
<div id="dialog"></div>
<div id="multi_dialog"></div>
<div id="new_windowID">{$new_windowID}</div>

{if $testserver}
	<div id="testserver" style="display: none;">true</div>
{else}
	<div id="testserver" style="display: none;">false</div>
{/if}
<div id="display_errors" style="display: none;">{$setting.display_errors}</div>
<div id="lang_priority" style="display:none;">1</div>
<div id="lang_default" style="display:none;">{$legacy_lang_default}</div>

<div id="session"></div>

<script src="js/function.js?{$timestamp}"></script>

<script src="appjs.php?class=base&{$timestamp}"></script>

{foreach $js_class_list as $jsclass}
	<script src="appjs.php?class={$jsclass}&{$timestamp}"></script>
{/foreach}


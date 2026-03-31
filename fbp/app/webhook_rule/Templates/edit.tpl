<form id="webhook_rule_edit_form_{$data.id}">
	<input type="hidden" name="id" value="{$data.id}">

	<div>
		<h6 class="lang">{t key="webhook_rule.channel"}</h6>
		{html_options name="channel" options=$channel_opt selected=$data.channel}
		<p class="error_message lang error_channel"></p>
	</div>

	<div>
		<h6 class="lang">{t key="webhook_rule.keyword"}</h6>
		<input type="text" name="keyword" value="{$data.keyword}">
		<p style="font-size:12px;color:#666;">{t key="webhook_rule.data_type_help"}</p>
		<p class="error_message lang error_keyword"></p>
	</div>

	<div>
		<h6 class="lang">{t key="webhook_rule.match_type"}</h6>
		{html_options name="match_type" options=$match_type_opt selected=$data.match_type}
		<p class="error_message lang error_match_type"></p>
	</div>

	<div>
		<h6 class="lang">{t key="webhook_rule.action_class"}</h6>
		<input type="text" name="action_class" value="{$data.action_class}">
		<p class="error_message lang error_action_class"></p>
	</div>

	<div>
		<h6 class="lang">{t key="webhook_rule.status"}</h6>
		{html_options name="enabled" options=$enabled_opt selected=$data.enabled}
	</div>

	<button class="ajax-link lang" data-form="webhook_rule_edit_form_{$data.id}" data-class="{$class}" data-function="edit_exe">{t key="common.update"}</button>
</form>

<form id="webhook_rule_add_form">
	<div>
		<h6 class="lang">{t key="webhook_rule.channel"}</h6>
		{assign var=selected_channel value=$post.channel|default:0}
		{html_options name="channel" options=$channel_opt selected=$selected_channel}
		<p class="error_message lang error_channel"></p>
	</div>

	<div>
		<h6 class="lang">{t key="webhook_rule.keyword"}</h6>
		<input type="text" name="keyword" value="{$post.keyword|default:''}">
		<p style="font-size:12px;color:#666;">{t key="webhook_rule.data_type_help"}</p>
		<p class="error_message lang error_keyword"></p>
	</div>

	<div>
		<h6 class="lang">{t key="webhook_rule.match_type"}</h6>
		{assign var=selected_match value=$post.match_type|default:'exact'}
		{html_options name="match_type" options=$match_type_opt selected=$selected_match}
		<p class="error_message lang error_match_type"></p>
	</div>

	<div>
		<h6 class="lang">{t key="webhook_rule.action_class"}</h6>
		<input type="text" name="action_class" value="{$post.action_class|default:''}">
		<p class="error_message lang error_action_class"></p>
	</div>

	<div>
		<h6 class="lang">{t key="webhook_rule.status"}</h6>
		{assign var=selected_enabled value=$post.enabled|default:1}
		{html_options name="enabled" options=$enabled_opt selected=$selected_enabled}
	</div>

	<button class="ajax-link lang" data-form="webhook_rule_add_form" data-class="{$class}" data-function="add_exe">{t key="common.add"}</button>
</form>

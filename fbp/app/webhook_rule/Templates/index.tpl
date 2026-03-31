<div>
	<div style="float:right;margin-bottom:8px;">
		<button class="ajax-link lang" data-class="{$class}" data-function="add">{t key="webhook_rule.add_button"}</button>
	</div>
</div>
<div style="clear:both;"></div>

<table class="moredata" style="margin-top:10px;">
	<thead>
		<tr class="table-head">
			<th style="width:5%;"></th>
			<th class="lang" style="width:10%;">{t key="webhook_rule.channel"}</th>
			<th class="lang" style="width:20%;">{t key="webhook_rule.keyword"}</th>
			<th class="lang" style="width:10%;">{t key="webhook_rule.match"}</th>
			<th class="lang" style="width:20%;">{t key="webhook_rule.action_class"}</th>
			<th class="lang" style="width:10%;">{t key="webhook_rule.status"}</th>
			<th style="width:25%;"></th>
		</tr>
	</thead>
	<tbody class="webhook-rule-sort">
		{foreach $items as $item}
			<tr id="{$item.id}" style="background:#FFF;">
				<td><span class="ui-icon ui-icon-arrowthick-2-n-s wr-handle"></span></td>
				<td>{$channel_opt[$item.channel]|default:$item.channel}</td>
				<td>{$item.keyword}</td>
				<td>{$item.match_type}</td>
				<td>{$item.action_class}</td>
				<td>{if $item.enabled == 1}{$enabled_opt[1]}{else}{$enabled_opt[0]}{/if}</td>
				<td>
					<button class="ajax-link listbutton" data-class="{$class}" data-function="delete" data-id="{$item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>
					<button class="ajax-link listbutton" data-class="{$class}" data-function="edit" data-id="{$item.id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

<script>
$(function () {
	$(".webhook-rule-sort").sortable({
		handle: ".wr-handle",
		axis: "y",
		update: function () {
			var log = $(this).sortable("toArray");
			var fd = new FormData();
			fd.append('class', '{$class}');
			fd.append('function', 'sort');
			fd.append('log', log);
			appcon('app.php', fd);
		}
	});
});
</script>

<div>
	<div style="float:right;margin-bottom:8px;">
		<button class="ajax-link lang" data-class="{$class}" data-function="add">{t key="embed_app.add_button"}</button>
	</div>
</div>
<div style="clear:both;"></div>

<table class="moredata" style="margin-top:10px;">
	<thead>
		<tr class="table-head">
			<th style="width:5%;"></th>
			<th style="width:18%;">{t key="common.title"}</th>
			<th style="width:12%;">{t key="embed_app.embed_key"}</th>
			<th style="width:24%;">{t key="common.class_name"}</th>
			<th style="width:8%;">{t key="common.status"}</th>
			<th style="width:25%;">{t key="embed_app.snippet"}</th>
			<th style="width:8%;"></th>
		</tr>
	</thead>
	<tbody class="embed-app-sort">
		{foreach $items as $item}
			<tr id="{$item.id}" style="background:#FFF;">
				<td><span class="ui-icon ui-icon-arrowthick-2-n-s ea-handle"></span></td>
				<td>{$item.title|escape}</td>
				<td>{$item.embed_key|escape}</td>
				<td>{$item.class_name|escape}</td>
				<td>{if $item.enabled == 1}{$enabled_opt[1]}{else}{$enabled_opt[0]}{/if}</td>
				<td>
					<button class="ajax-link" data-class="{$class}" data-function="snippet_dialog" data-id="{$item.id}" style="float:right;">{t key="embed_app.generate"}</button>
				</td>
				<td>
					<button class="ajax-link listbutton" data-class="{$class}" data-function="delete" data-id="{$item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>
					<button class="ajax-link listbutton" data-class="{$class}" data-function="edit" data-id="{$item.id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

{literal}
<script>
$(function () {
	$(".embed-app-sort").sortable({
		handle: ".ea-handle",
		axis: "y",
		update: function () {
			var log = $(this).sortable("toArray");
			var fd = new FormData();
			fd.append('class', '{/literal}{$class|escape:'javascript'}{literal}');
			fd.append('function', 'sort');
			fd.append('log', log);
			appcon('app.php', fd);
		}
	});
});
</script>
{/literal}

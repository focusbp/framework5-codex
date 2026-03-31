<div>
	<div style="float:right;margin-bottom:8px;">
		<button class="ajax-link lang" data-class="{$class}" data-function="add">{t key="panel_constants.add_button"}</button>
	</div>
</div>
<div style="clear:both;"></div>

<table class="moredata" style="margin-top:10px;">
	<thead>
		<tr class="table-head">
			<th class="lang" style="width:35%;">{t key="panel_constants.array_name"}</th>
			<th style="width:45%;">{t key="panel_constants.values"}</th>
			<th style="width:20%;"></th>
		</tr>
	</thead>
	<tbody>
		{foreach $items as $item}
			<tr>
				<td>{$item.array_name}</td>
				<td>
					{if $item.value_sets|@count > 0}
						<table style="width:100%;border-collapse:collapse;table-layout:fixed;margin-top:2px;">
							<colgroup>
								<col style="width:20%;">
								<col style="width:60%;">
								<col style="width:20%;">
							</colgroup>
							<tbody>
								{foreach $item.value_sets as $set}
									<tr>
										<td style="border:none;padding:2px 0;">{$set.key}</td>
										<td style="border:none;padding:2px 0;">{$set.title}</td>
										<td style="border:none;padding:2px 0;">
											<span style="display:inline-block;width:12px;height:12px;border:1px solid #999;vertical-align:middle;background-color:{$set.color};"></span>
										</td>
									</tr>
								{/foreach}
							</tbody>
						</table>
					{/if}
				</td>
				<td>
					<button class="ajax-link listbutton" data-class="{$class}" data-function="delete" data-id="{$item.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>
					<button class="ajax-link listbutton" data-class="{$class}" data-function="edit" data-id="{$item.id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

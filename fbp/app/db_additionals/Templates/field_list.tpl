
<div id="tabs_{$db.tb_name}">
  <ul>
    <li><a href="#tabs-1">{t key="db_additionals.field_reference.table"}</a></li>
    <li><a href="#tabs-2">{t key="db_additionals.field_reference.text"}</a></li>
  </ul>

<div id="tabs-1">
<h4>{$db.tb_name}</h4>

<table style="font-size:12px;">
{foreach $field_list as $p}
	<tr>
	<td style="width:30%;">
		{$p.parameter_name}
	</td>
	<td style="width:30%;">
		{$p.parameter_title}
	</td>
	<td>
		{$p.type}
		
		{if $p.option_name != ""}
		<h6 style="margin-top:5px;font-size:12px;">{$p.option_name}</h6>
		<table style="margin-top:5px;">
			{foreach $p.options as $opt}
				<tr>
					<td>{$opt.value}</td>
					<td>{$opt.title}</td>
				</tr>
			{/foreach}
		</table>
		{/if}
		
	</td>
	</tr>
{/foreach}
</table>
</div>

<div id="tabs-2">
<h4>{$db.tb_name}</h4>
<textarea style="font-size:12px;">
{foreach $field_list as $p}
{$p.parameter_name} {$p.parameter_title} {$p.type} {if $p.option_name != ""} {$p.option_name}({foreach $p.options as $opt}{$opt.value}:{$opt.title}{if not $opt@last}, {/if}{/foreach}){/if}

{/foreach}
</textarea>
</div>
</div>

  <script>
  $( function() {
    $( "#tabs_{$db.tb_name}" ).tabs();
  } );
  </script>

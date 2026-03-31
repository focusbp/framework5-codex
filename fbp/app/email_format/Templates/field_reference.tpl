
<h5>{t key="email_format.field_reference"}</h5>

{html_options name="db_id" options=$db_opt selected=$db_id id="db_id_dropdown"}


<table style="margin-top:20px;">
	{foreach $field_list as $f}
		<tr>
			{if $f.type_condition == 1}
				{if $f.type == "datetime"}
					<td>{$f.parameter_title}: {ldelim}${$db.tb_name}.{$f.parameter_name}|date_format:"%Y/%m/%d %H:%M"{rdelim}</td>
				{else}
					<td>{$f.parameter_title}: {ldelim}${$db.tb_name}.{$f.parameter_name}{rdelim}</td>
				{/if}
			{else if $f.type_condition == 2}
				<td>{$f.parameter_title}: {ldelim}${$f.constant_array_name}[${$db.tb_name}.{$f.parameter_name}]{rdelim}</td>
			{/if}
				
		</tr>
	{/foreach}
</table>

<script>
	$("#db_id_dropdown").on("change",function(){
		var fd = new FormData();
		fd.append("class","email_format");
		fd.append("function","database_field_reference");
		fd.append("db_id",$(this).val());
		appcon("app.php",fd);
	});
</script>
				


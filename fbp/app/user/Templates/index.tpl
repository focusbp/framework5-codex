<button class="ajax-link" data-class="user" data-function="append" style="margin-bottom:20px;">{t key="user.add_button"}</button>
<button class="ajax-link" data-class="{$class}" data-function="upload_csv">{t key="user.upload_csv_button"}</button>

<form class="search_form" id="search_form" style="margin-top:20px;">
	<span>{t key="user.search_label"}：</span><input type="text" name="search_word" value="{$search_word}" style="width:200px;">
	<button class="ajax-link" data-class="user" data-function="page" data-form="search_form" style="margin-top:0px;">{t key="common.search"}</button>
</form>

<table style="margin-top:20px;" class="moredata">
	<tr>
		<th>{t key="user.type"}</th>
		<th>{t key="user.name"}</th>
		<th>{t key="user.login_id"}</th>
		<th></th>
		<th></th>
	</tr>

	{foreach $user_list as $data}
		<tr class="status{$data.status}">
			<td><span style="background: {$user_type_opt_colors[$data.type]};" class="user_type_bg">{$user_type_opt[$data.type]}</span></td>
			<td>{$data.name}</td>
			<td>{$data.login_id}<br />
			</td>
			<td>
				<button class="ajax-link" data-class="user" data-function="password_reset" data-id="{$data.id}">{t key="user.send_reset_link"}</button>
			</td>
			
			<td>
			<button class="dialog-link listbutton" data-url="app.php" data-class="user" data-function="edit" data-id="{$data.id}" style="float:right;color:black;"><span class="ui-icon ui-icon-pencil"></span></button>

			<button class="dialog-link listbutton" data-url="app.php" data-class="user" data-function="delete" data-id="{$data.id}" style="float:right;color:black;margin-right:5px;"><span class="ui-icon ui-icon-trash"></span></button>
			</td>
		</tr>
	{/foreach}
</table>

{if $is_last == false}
	<div class="ajax-auto" data-form="search_form" data-class="user" data-function="page" data-max="{$max}"></div>
{/if}

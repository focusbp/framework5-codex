<form id="input_form" style="width:100%;">

    <table>
        <tr>
            <td>{t key="user.name"}</td>
            <td><input type="text" name="name" value="{$data.name}" style="width:400px;"></td>
        </tr>

        <tr>
            <td>{t key="user.login_id"}</td>
            <td><input type="text" name="login_id" value="{$data.login_id}" style="width:400px;">
                <p class="error">{$err_login_id}</p>
            </td>
        </tr>
        <tr>
            <td>{t key="user.password_setup"}</td>
            <td>
                <p style="margin:0;">{t key="user.password_setup_help"}</p>
            </td>
        </tr>

		<tr>
			<td>{t key="user.email"}</td>
			<td><input type="text" name="email" value="{$data.email}" style="width:400px;">
				<p class="error">{$err_email}</p>
			</td>
		</tr>


		<tr>
			<td>{t key="user.type"}</td>
			<td>
				{html_options
            name="type"
            options=$user_type_opt
            selected=$data.type
            style="width:400px;"
				}
			</td>
		</tr>

		<tr class="permission-row">
			<td>{t key="user.developer"}</td>
			<td>
				{html_options
            name="developer_permission"
            options=$developer_permission_opt
            selected=$data.developer_permission
				}
			</td>
		</tr>

		<tr class="permission-row">
			<td>{t key="user.release_backup"}</td>
			<td>
				{html_options
            name="data_manager_permission"
            options=$data_manager_permission_opt
            selected=$data.data_manager_permission
				}
			</td>
		</tr>

    </table>


</form>

<button class="ajax-link" data-class="user" data-function="append_exe" data-form="input_form">{t key="common.save"}</button>

<script>
// type によって権限行の表示/非表示を切り替え
	function togglePermissionRows() {
		var typeVal = $('select[name="type"]').val(); // type の値取得

		if (typeVal === '1') {
			$('.permission-row').show();
		} else {
			$('.permission-row').hide();
		}
	}

	$(function () {
		// 初期表示
		togglePermissionRows();

		// type 変更時に反映
		$('body').on('change', 'select[name="type"]', function () {
			togglePermissionRows();
		});
	});


</script>

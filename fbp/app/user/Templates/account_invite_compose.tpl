<form id="user_account_invite_compose_form">
	<input type="hidden" name="id" value="{$data.id}">
	<input type="hidden" name="dialog_name" value="{$dialog_name}">

	<p>&lt;&lt;{$send_to_email|escape}&gt;&gt; {t key="user.account_invite.send_to_suffix"}</p>
	<p>{t key="user.account_invite.delivery_notice"}</p>

	<p>{t key="user.account_invite.subject"}</p>
	<input type="text" name="subject" value="{$subject|escape}" style="width:100%;">
	<div class="error_subject"></div>

	<p>{t key="user.account_invite.body"}</p>
	<textarea name="body" style="width:100%;height:320px;white-space:pre;">{$body|escape}</textarea>
	<div class="error_body"></div>
</form>

<button class="ajax-link" data-class="user" data-function="account_invite_send_exe" data-form="user_account_invite_compose_form">{t key="user.account_invite.send_button"}</button>

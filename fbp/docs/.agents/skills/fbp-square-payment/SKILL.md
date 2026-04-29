---
name: fbp-square-payment
description: Implement Square Payment flows in FBP using show_square_dialog and callback-based payment execution.
---

# fbp-square-payment

## trigger conditions
- `db_additionals` で `code_type=Square Payment` を追加する
- `show_square_dialog()` を使ったカード決済を実装する
- Square決済の成否ダイアログを実装・検証する
- `public_pages` のカート/会員公開導線で新規カード登録を伴う決済を実装する

## workflow
1. 決済クラスの `run()` で `show_square_dialog("<class>", "pay", $callback_params)` を呼ぶ。
2. `pay()` で `get_square_callback_parameter_array()` を取得し、`square_regist_customer()` -> `square_regist_card()` -> `square_payment()` を順に実行する。
3. 成否で `close_square_dialog()` 後に `show_multi_dialog()` を返す。
4. 例外時は `show_square_dialog()` を再表示し、エラーメッセージを返す。
5. `db_additionals_add` で `code_type:[5]` を設定し、対象テーブルにボタン導線を追加する。

## public_pages cart pattern
- 公開カート導線では、Square callback 関数の先頭で `require_public_line_member()` を使わない。公開エラーページへ流れてダイアログ上のエラーが見えなくなる。
- callback 側はまず `get_square_callback_parameter_array() ?? []` を取り、その後 `sync_public_line_member()` など session 直参照ベースで公開会員文脈を復元する。
- 公開会員文脈を復元できない場合も `show_square_dialog()` に戻し、公開エラーページへ遷移させない。
- `square_regist_customer()` / `square_regist_card()` が空文字や `null` を返すケースを成功扱いにしない。`square_get_error()` を優先して `Exception` に変換し、DB更新前に止める。
- `Throwable` catch では `show_square_dialog()` を再表示し、`$e->getMessage()` が空なら `square_get_error()` を補完する。

### sample
```php
function register_card_and_place_order(Controller $ctl) {
	$param = $ctl->get_square_callback_parameter_array() ?? [];
	$line_member = $this->sync_public_line_member($ctl);
	if ((int) ($line_member["id"] ?? 0) <= 0) {
		$ctl->show_square_dialog(
			"public_pages",
			"register_card_and_place_order",
			$param,
			"購入セッションを確認できませんでした。もう一度お試しください。"
		);
		return;
	}

	try {
		$square_customer_id = (string) $ctl->square_regist_customer(
			(string) ($param["name"] ?? ""),
			(string) ($param["email"] ?? ""),
			(string) ($param["address"] ?? "")
		);
		if ($square_customer_id === "") {
			throw new Exception((string) ($ctl->square_get_error() ?: "Square顧客登録に失敗しました"));
		}

		$square_card_id = (string) $ctl->square_regist_card($square_customer_id);
		if ($square_card_id === "") {
			throw new Exception((string) ($ctl->square_get_error() ?: "Squareカード登録に失敗しました"));
		}

		$ctl->close_square_dialog();
		// ここで保存/決済/完了画面へ進める
	} catch (Throwable $e) {
		$error_message = trim($e->getMessage());
		if ($error_message === "") {
			$error_message = trim((string) $ctl->square_get_error());
		}
		if ($error_message === "") {
			$error_message = "Squareカード登録に失敗しました";
		}
		$ctl->show_square_dialog("public_pages", "register_card_and_place_order", $param, $error_message);
	}
}
```

## constraints
- 秘密鍵/アクセストークンをコード直書きしない（`setting` 利用）。
- 最低限の金額バリデーション（0以下不可）を入れる。
- `run()` で受け取る `id` は暗号化IDとして扱い、`decrypt()` 後に利用する。
- 公開導線では、Square callback 失敗時に `show_error_page()` や通常 `res_redirect()` へ逃がさず、まずダイアログへ戻す。

---
name: fbp-dialog
description: Implement FBP dialog-based UI flows with ajax-link/invoke-function, validation, and error rendering rules.
---

# fbp-dialog

## trigger conditions
- `show_multi_dialog()` を使う画面を作る
- Ajax ダイアログ遷移や保存処理を実装する
- エラー表示が絡むフォームを作る

## workflow
1. ダイアログ表示は `show_multi_dialog()` で開始。
2. ボタンは `ajax-link` + `invoke-function` で接続。
3. バリデーション時は `res_error_message()` を設定し `return`。
4. 必要なら `app_call` でHTMLに `error_*` 要素があるか確認。

## dialog layout policy
- `show_multi_dialog($dialog_name, $template, $title, $width, $fixed_bar_template, $options)` の引数役割を明確に使い分ける。
- ダイアログ上部タイトルは第3引数 `title` で指定する（テンプレート内に重複タイトルを増やさない）。
- ダイアログ幅は第4引数 `width` で指定する。
- 上部固定操作は第5引数 `fixed_bar_template` に分離して指定する（例: `"_fixed_bar.tpl"`）。
- ダイアログメニュー（タブ）は `add_tab()` を使って `.multi_dialog_tab_area` に追加する。
- 配置指針: 全体操作=固定バー、画面名=title、内容切替=タブ、フォーム本文=contents。

## constraints
- エラー時の再描画（`show_multi_dialog` 再実行、`reload_area`）を禁止。
- `fields_form_direct` 使用時は項目ごとに `error_項目名` を用意する。
- `fields_form_original` / 手書きinput / checkbox / textarea を含む、POST対象の全入力項目にも `error_項目名` を必ず配置する。
- 実装完了前に「`res_error_message(field, ...)` の `field` 名」と「テンプレート上の `error_field` クラス」が1対1で存在することを確認する（不足がある状態で完了扱いにしない）。
- `res_error_message()` を使う場合、表示先タグ（`error_項目名`）が存在することを必ず事前確認する。表示タグを設置できない導線（フォーム未描画前・一覧ボタン直叩き等）では `show_notification_text()` を使う。
- 確認表示・詳細表示の値描画は `fields_view_direct` を優先し、手書き展開は必要最小限にする。
- DBに登録された file/image を `<img>` で直接表示する場合、テンプレートから保存パスを直参照せず、表示元クラスに `view_image(Controller $ctl)` などの画像表示関数を必ず実装してそこを通す。
- 上記の画像表示関数では、受け取った識別子や暗号化pathを検証し、`is_saved_file()` 確認後に `res_saved_image()` を返す。ダウンロード用は別に `download_file()` を用意して `res_saved_file()` を返す。
- DB画像の `<img>` には原則 `max-width:500px;` を付け、縦サイズは固定しない。`height` / `max-height` で縦横比を崩さない。
- 固定バー上の非ajaxボタン（例: `type="button"`）をJSで扱う場合、`.multi_dialog` スコープで要素取得してイベントを張る。
- `ajax-link` でフォーム値をPOSTする画面は、テンプレート全体を `<form onsubmit="return false;">...</form>` で囲み、対象入力の `error_*` 要素を必ず配置する。

---
name: fbp-public-pages
description: Build and operate public_pages with login-free entry points, secure URL parameter design, and CLI-first verification.
---

# fbp-public-pages

## trigger conditions
- `public_pages` クラスを新規作成・修正する
- ログイン不要の公開導線（一覧/申込み/決済/完了）を実装する
- LINE・Webhook・メール等から公開URLを発行する

## workflow
1. `__construct` で `set_check_login(false)` を設定し、公開入口関数を決める。
2. 入口関数で公開パラメータ（例: `id`）を受け、`decrypt` して session に公開ユーザー文脈をセットする。
3. 公開ページ全体表示は `show_public_pages("<contents.tpl>", "<head.tpl>", "<contents_header.tpl>", "<contents_footer.tpl>")` を優先する。不要な差し込みは `null` で省略してよい。操作系は `show_multi_dialog()` + `ajax-link` + `invoke-function` で遷移を組む。
4. 表示テンプレートは `fields_view_direct` を第一候補とし、手書き値展開はリンク化・複合レイアウトなど必要最小限に限定する。
   画像を一覧で軽量表示したい場合は `fields_view_direct ... use_thumbnail=true` を使い、タグ単位でサムネイル表示を指定する。
5. 保存・決済前に必須チェックを行い、異常時は `show_notification_text()` または `res_error_message()` で即 `return`。
   checkbox 項目は配列前提で扱い、会員可否のような判定は `count($line_member["xxx"] ?? []) > 0` で判定する。
6. 外部導線（LINE webhook 等）では `$ctl->get_APP_URL("public_pages", "<function>", ["id" => $id_enc])` でURLを生成する。
7. `app_call` / `app_check` で公開導線を検証し、更新系は `data_get` / `data_list` で反映確認する。
8. 公開入口の識別子は初回だけ受け、復号後は session に保存して以後の内部導線では再送しない。
9. 公開検索や絞り込みで URL に出したくない値は、`GET` ではなく `POST -> session` で保持し、表示時に復元する。

## Public Assets
- `public_pages` で固定画像を使う場合は、`Public Assets` 管理で登録された `asset_key` を使う。
- 実ファイル名（`stored_filename`）をテンプレートやコードに直書きしない。
- ウィザードの `新規ページ追加` / `共通デザイン` で `Public Assets` が選択された場合、プロンプトの `【使用するPublic Assets】` に列挙された `asset_key` を前提に実装する。
- 画像URLは文字列連結せず、`$ctl->get_APP_URL("public_asset_media", "view", ["key" => $asset_key])` を使う。
- Smarty テンプレートでは `src` / `href` に URL を直書きせず、`{public_asset_url key="asset_key"}` または `{public_asset_img key="asset_key" ...}` を優先する。`&amp;amp;` のような二重エスケープ回避に有効。
- `public_asset_media` は `asset_key` または `id` で配信できるが、公開ページ実装では可読性のため `asset_key` を優先する。
- `header.tpl` / `footer.tpl` / 共通LP / ヒーロー画像など、複数ページで使う素材は `Public Assets` に寄せる。
- `public_pages` の新規作成プロンプトに `Public Assets` が含まれる場合は、選択された素材をどこで使うか（ヘッダ、KV、セクション背景、ロゴ等）を制作内容に沿って具体化して実装する。

## common layout rules
- 公開ページ全体のラップは `fbp/Templates/publicsite_index.tpl` を前提にする。
- 共通head周りの調整は `fbp/Templates/publicsite_header.tpl` を優先する。
- 共通footer周りの調整は `fbp/Templates/publicsite_footer.tpl` を優先する。
- `publicsite_index.tpl` は公開ページ共通の骨組みに限定し、ブランド名・会社名・ロゴURLなどを固定で持たせない。
- 可視ヘッダの内容は `publicsite_header.tpl`、可視フッタの内容は `publicsite_footer.tpl` 側で持つ。
- `publicsite_index.tpl` 側には `html_header` / `contents_header` / `contents` / `contents_footer` の配置枠だけを置く。
- 公開側の共通デザインを変更する主対象は `show_public_pages()` の第3引数 / 第4引数で渡すテンプレートと `classes/app/public_pages/style.css` とする。
- `publicsite_header.tpl` / `publicsite_footer.tpl` が未作成でも壊れないように、必要なら空の class 付き要素で成立する構造にする。
- head内のCSS追加は `publicsite_header.tpl` 側に置く。
- 末尾scriptや共通JS追加は `publicsite_footer.tpl` 側に置く。
- `publicsite_footer.tpl` 既存の script / DOM 責務は維持したまま拡張する。
- 公開側の共通CSSは `classes/app/public_pages/style.css` に置いてよい。通常の管理画面では自動読込されない。
- 公開側の ajax 保存後に `show_public_pages()` で完了画面を出す場合、内部では `display()` が使われる。公開導線では `_DISPLAY` 復元が安定する前提で、このワンパターンを優先してよい。
- `public_pages` の各本文テンプレートに、共通ヘッダ・フッタを重複実装しない。
- 共通デザイン作製時は、まず `publicsite_index.tpl / publicsite_header.tpl / publicsite_footer.tpl` の責務を確認してから編集範囲を決める。
- `show_public_pages()` 前提の構造を崩さない。共通レイアウト変更はこの前提の中で行う。
- 共通導線のリンク先が未確定な段階では、後から差し替えやすい実装を優先する。
- 共通導線は仮リンク、TODOコメント、差し替え用プレースホルダ構造で一旦実装してよい。
- 共通メニューは `public_pages_registry` から取得して描画する前提を優先する。
- メニュー対象は `enabled=1` かつ `show_in_menu=1` のレコードを使う。
- メニュー表示名は `menu_label` を優先し、未設定時は `title` を使う。
- メニュー順は `menu_sort` 昇順を前提にする。
- まだ公開ページが揃っていない段階でも、ヘッダ側は `public_pages_registry` のメニュー取得に寄せておく。

## URL design rules
- URLは文字列連結せず、必ず `$ctl->get_APP_URL()` を使う。
- `id` などの公開パラメータは平文IDを使わず `encrypt()` した値を渡す。
- 受け側は `GET("id")` / `decrypt_post("id")` で復号し、対象が取れない場合は公開エラーを返して終了する。
- 公開フォームの継続導線で同じ識別子を何度も使う場合は、初回入口で暗号化済み値を session に保持してよい。`続けて入力する` リンクはパラメータ省略で同一 function に戻すほうが安定する。
- `public_pages` の関数名が URL 導線単位になるため、用途ごとに関数を分ける。
- URL発行側と受け側で、クラス名・関数名・パラメータキー（例: `id`）を必ず一致させる。
- URLの基本形は `/<class>*<function>`。例: `public_pages -> lp` の場合は `/public_pages*lp`。
- クエリ付き例: `$ctl->get_APP_URL("public_pages", "lp", ["id" => $id_enc])` は `/public_pages*lp?id=<encrypted>` 形式になる。
- このフレームワークでは `/<class>*<function>&key=value` や `/<class>*<function>?key=value` を公開URLの正常系として扱う。`*` による class/function 表記や、先頭が `?class=` でないこと自体を異常扱いしない。

## constraints
- 公開導線でも `_buttons_prompt_form.tpl` の allowlist に従う。
- エラー時に `show_multi_dialog()` 再実行や `reload_area()` で再描画しない。
- 公開側の表示は file/image に限らず `fields_view_direct` を優先する。
- 画像の一覧表示は `fields_view_direct` の `use_thumbnail=true` を優先し、コントローラ側で `_use_thumbnail` を広域代入しない。
- 公開ページのレイアウトラップ（`publicsite_index.tpl`）は `show_public_pages()` に集約し、各 `public_pages` クラスで `template_dir` を直接切り替えない。
- 公開側エントリクラス名は必ず `public_pages` を使用する（別クラス名で公開導線を作らない）。
- 公開側の通常 `form` / 通常リンクは `appcon()` を通らない。会員文脈が必要な内部導線は、原則 `ajax-link` / `invoke-function` / `appcon()` 経由を優先する。
- 公開側の通常 `<a href>` に状態維持用パラメータを付けて引き回す運用は原則禁止。検索エンジンのクロールや重複URL増殖の原因になる。

## recommended base shape
- 公開ページの基本形は `publicsite_index.tpl` を骨格、`publicsite_header.tpl` / `publicsite_footer.tpl` を共通head・共通footer、`classes/app/public_pages/style.css` を公開側共通CSSとして分離する。
- `show_public_pages()` の第2引数は head 追加、第3引数は本文前ブロック、第4引数は本文後ブロックとして使う。
- ページ固有の見出しや補足導線は `contents_header.tpl` / `contents_footer.tpl` に切り出すと差し替えや再利用がしやすい。
- 共通デザイン案件では、第3引数 / 第4引数に渡している共通テンプレートを主に編集し、style.css の変更指示は別に持たせると実装が安定する。
- フォーム本体は本文テンプレートに置き、共通の案内・ナビ・メニュー・補足は前後テンプレートへ寄せる。
- 完了画面も原則 `show_public_pages()` でそろえ、特殊な事情がない限り `res_redirect()` に逃がさない。
- `続けて入力する` のような戻りリンクでは、復号用の識別子を URL に毎回載せ直さず session 保持へ寄せると壊れにくい。
- 一覧→詳細→一覧、検索、絞り込み、ページングなどの内部導線は、URLパラメータの引き回しより session 保持を優先する。
- 公開フォームの基本例は `references/orders_contact_sample.md` を参照する。

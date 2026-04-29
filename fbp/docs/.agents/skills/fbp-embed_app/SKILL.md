---
name: fbp-embed_app
description: Create and maintain router-based embed_app implementations, tag generation, and CLI verification.
---

# fbp-embed_app

## trigger conditions
- embed_app（router方式）を新規作成・更新する
- 埋め込みタグや origin 条件の調整が必要

## workflow
1. `embed_app_list` で既存確認。
2. router方式で `embed_app` を作成/編集。
3. 初期表示は `page()` から `display("page.tpl")` で返す。
4. 埋め込み内の Ajax ステップ遷移は `display()` ではなく `reload_area()` を基本にする。
5. 複数ステップ導線は「外枠 `page.tpl` + 差し替え部分テンプレート」の構成にする。
6. 状態保持は原則 hidden で引き回す。
7. バリデーションエラーは `res_error_message()` を返して即 `return` を基本にし、入力エラー時に `reload_area()` でフォーム全体を再描画しない。
8. 埋め込みタグを生成し、origin付きで動作確認。
9. チェックリスト（URL・キー・origin・Ajax遷移・バリデーション表示）を満たすことを確認。

## constraints
- 既存 `db_widget` がある場合は移行影響を確認する。
- `ajax-link` は JSON 応答前提で動くため、埋め込み内の段階遷移で `display()` を使うと意図通りに切り替わらないことがある。
- Ajax 遷移時は埋め込み領域のラッパー要素を固定し、`reload_area("#wrapper", "_step.tpl")` のように部分差し替えする。
- 入力エラー時に `errormessage` と `reload_area()` を同時に返すと、埋め込み先のJSでエラー表示が消えたり不安定になることがある。入力エラーでは再描画せず、その場の DOM に対して `res_error_message()` だけ返す。
- `reload_area()` を使うのは、ステップが本当に切り替わるときだけに限定する。
- `embed_app` ではセッションは使わない。
- 特に cross-site iframe の埋め込みではセッション/cookie 依存は不安定になりやすいため、状態は hidden、署名付き token、DB 一時保存などで持つ。

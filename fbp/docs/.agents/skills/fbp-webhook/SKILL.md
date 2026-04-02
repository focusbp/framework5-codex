---
name: fbp-webhook
description: Implement and operate webhook-driven integrations in FBP, including rule registration and verification.
---

# fbp-webhook

## trigger conditions
- 外部イベント受信（Webhook）を実装する
- `webhook_rule` の登録/更新が必要
- LINE連携などWebhook起点処理を扱う

## workflow
1. 受信要件と署名要件を整理。
2. 受信クラス/処理を実装。
3. `webhook_rule` を登録。
4. CLIまたは検証リクエストで受信から処理完了まで確認。

## line patterns
- LINE Bot の `keyword -> action_class` 型を作る場合は `references/line_bot_member_link.md` を読む。
- LINE user_id と会員DBを連結する場合、まず `webhook_line` 標準の `getting_member` 解決を前提にする。
- `getting_member` は LINE の生イベントではなく、`webhook_line` が前処理として呼ぶ内部解決処理。
- 既存アプリ互換のため、`[getting_member]` / `match_type=data_type` の `webhook_rule` がある場合はそれを優先できる。
- 会員連携済みのキーワード処理は、`line_webhook_context.line_member` を前提に `public_pages` URL を返す形を基本とする。
- Wizard 運用では、会員連携の標準構成を `line_member / userid / line_name / name` とする。
- `LINE用会員データベース作製` は固定テンプレートとして扱い、上記のテーブル名・フィールド名で実装する。新規案件では `getting_member` 用 `webhook_rule` / `action_class` は通常不要。
- `Line Bot処理追加` / `Line Bot処理変更` で会員連携を前提にする場合も、特別な指示がない限り `line_member / userid / line_name / name` を標準前提として扱う。
- `webhook_rule` のクラスと公開画面のクラスは分離を基本とし、同一クラスにまとめない。
- 代わりに、`webhook_rule class` と `public_pages function` を対応ペアとして設計・命名する。
- 例: `line_webhook_rule_event -> public_pages::event_list`, `line_webhook_rule_member_search -> public_pages::member_search`

## constraints
- 機密情報（シークレット、トークン）をコード直書きしない。
- LINE 連携では `webhook_rule.channel=0` を使う。
- `webhook_rule` は `channel + keyword` 重複を避ける。
- `getting_member` を実装する場合は、会員検索キー・表示名保存先・未登録時の新規作成方針を明示する。

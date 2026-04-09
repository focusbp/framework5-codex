---
name: fbp-server-error-autofix
description: Use when handling server_error_autofix tasks that claim one server error, repair it in NetBeansProjects, sync to web, run only the minimum necessary verification, and return a single JSON result without drifting into extra exploration.
---

# fbp-server-error-autofix

## trigger conditions
- `server_error_autofix.sh` で Codex に 1 件の server error を修正させる
- `claim` 済みの server error をもとに、最小確認で素早く結果 JSON を返したい
- `file_path` / `http_host` だけでは framework か app かを決めきれず、修正実態ベースで `app_name` を決めたい

## workflow
1. まず `file_path`, `class_name`, `function_name`, `message` から、直接対応するローカル実装を特定する。
2. `NetBeansProjects` 側だけを編集する。`web/*` を編集元にしない。
3. framework と app のどちらを直したかは、`実際に変更したコード` で判断する。
4. 修正後は対象に応じて `copy_to_web_framework.sh` または `copy_to_web.sh` を必ず実行する。
5. 検証は最小限に留める。完了条件を満たしたら追加探索せずに JSON を返す。

## completion rules
- 基本の終了条件は `修正 -> web反映 -> 最小検証1回 -> JSON返却`
- `ParseError` / `syntax error` は、原則 `php -l` を主検証にする
- 必要なら軽い `app_call` または `app_check` を 1 回だけ行う
- 関連 CLI や探索を広げすぎない
- `release_fw5.sh` や `release_project.sh` は自分では実行しない

## app_name rules
- `file_path` や `http_host` はヒントであって決定条件ではない
- 最終的な `app_name` は、実際に修正したコードと実行した `copy_to_web*` に合わせて決める
- 返却値は `Framework` / `app-xxx` / `app-xxx, Framework`
- コード変更が不要なら `app_name` は空でもよい

## result JSON
- 最後の回答は前置きなしの JSON 1 個だけにする
- 形式:

```json
{"result_status":"release_waiting|failed|hold","memo":"...","copy_script":"copy_to_web.sh|copy_to_web_framework.sh|none","app_name":"app-xxx|Framework|app-xxx, Framework"}
```

## hold guidance
- 既に修正済みでコード変更が不要なら `hold`
- 再現しない、または修正方法を確定できずコード変更で対応できない場合も `hold`
- `memo` には原因・変更内容・検証結果・未実施事項だけを簡潔に残す

---
name: fbp-api-auth
description: Implement HMAC-SHA256 API authentication for FBP API classes and provide reproducible client-side test flows.
---

# fbp-api-auth

## trigger conditions
- APIクラスの認証追加・修正を行う
- HMAC署名仕様のクライアント検証が必要

## workflow
1. APIクラス名を `*_api` で統一。
2. エントリで `verify_api_request()` を実行し不正時は終了。
3. canonical文字列と署名仕様を合わせる。
4. bash/Pythonクライアントで署名付きリクエスト検証。
5. `task.sh` / `server_error.sh` / `sftp_api.sh` / `db_api.sh` など既存 script と同系統の API では、接続先切替を script 個別実装せず `~/scripts/lib/mgmt_api_auth.sh` に寄せる。
6. 本番 / テストの切替は `MGMT_API_MODE=production|test` を使い、env 解決の確認まで行う。
7. `db_api` の疎通確認は `tables` または `describe` を最初に使う。例: `MGMT_API_MODE=test ~/scripts/db_api.sh tables` / `MGMT_API_MODE=test ~/scripts/db_api.sh describe user user`

## constraints
- `api_secret` をログやレスポンスに出さない。
- 時刻ズレ許容やnonce再利用防止の方針を明示する。

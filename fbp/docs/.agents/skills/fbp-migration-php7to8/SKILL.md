---
name: fbp-migration-php7to8
description: Migrate app-framework5 and FBP projects from PHP 7 to PHP 8.x/8.5 by first driving ~/scripts/check_migration_php7to8.sh to OK, then fixing framework and app-specific warnings systematically.
---

# fbp-migration-php7to8

## trigger conditions
- PHP 7 系から PHP 8.2 / 8.5 へ移行する
- `Undefined array key`, `Deprecated`, `Smarty 5` エラーを潰す
- フレームワークや各プロジェクトを移行前に機械的に点検したい

## workflow
1. 最初に `~/scripts/check_migration_php7to8.sh <app_root>` を実行する。
2. 出力が `OK` になるまで、まずこのスクリプトで拾えたものを潰す。
3. `app-framework5` では framework 本体を先に直し、その後に各プロジェクト固有 `classes/app` を直す。
4. 修正後は `web` 側の `cli.php` で `app_call` を実行し、主要導線を再確認する。
5. `app_call` で再現したエラーのうち、固定ルートや固定パラメータで再現できるものは `check_migration_php7to8.sh` に追加する。
6. スクリプトで拾えない画面依存・データ依存の warning は、重要画面の追加確認で潰す。

## required defaults
- `NetBeansProjects -> web` の一方向同期を守る。
- 編集は `NetBeansProjects` 側、確認は `web` 側で行う。
- warning を隠さず、原因コードを直す。

## checker first
- `~/scripts/check_migration_php7to8.sh` は移行作業の初動に固定する。
- まず `PHP_NULLABLE`, `PHP_INPUT_ACCESS`, `PHP_LINT`, `RUNTIME` をゼロにする。
- `OK` になってから、未カバー画面の追加確認へ進む。
- `app_call` で見つけた再現可能な不具合は、その場限りで終わらせず checker に取り込めるかを毎回検討する。
- 追加しやすいものは `RUNTIME` チェックへ組み込み、次回以降の初動で自動検出できる状態にする。

## migration memo
- `RUNTIME` は即修正対象として扱う。
- `PHP_INPUT_ACCESS` は原則まとめて修正対象として扱う。PHP 8 系で warning に直結しやすい。
- 例外的に後回しにできるのは、直前で必ず値をセットしていることが明白なものだけに絞る。
- 各プロジェクト移行時は、checker の出力をそのまま backlog として使い、`RUNTIME` -> `PHP_INPUT_ACCESS` の順に潰す。

## references
- 詳細な修正観点、検索例、典型パターンは [references/checklist.md](references/checklist.md) を読む。

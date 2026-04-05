# PHP 7 to 8 Migration Checklist

## 目的
- `app-framework5` と各プロジェクトを PHP 7 系から PHP 8 系へ移行する際の実務チェックリストを残す。
- 特に `PHP 8.2` 以降で目立つ `Undefined array key`、`Deprecated`、`Smarty 5` 移行差分を先に潰す。

## 基本方針
- 先に `app-framework5` 本体を直す。
- その後に各プロジェクト固有 `classes/app` を直す。
- `NetBeansProjects -> web` の一方向同期を守る。
- `cli.php` は必ず `web` 側で実行する。
- 初動は必ず `~/scripts/check_migration_php7to8.sh <app_root>` から始め、`OK` になるまで潰す。

## 優先順位
1. `lib_ext` の PHP 8 系互換
2. `fbp/lib` の共通処理
3. `fbp/app` の管理画面系
4. 各プロジェクト固有 `classes/app`

## まず確認すること
- `~/scripts/check_migration_php7to8.sh <app_root>`
- `php8.2 -l` または `php8.5 -l` で構文エラーがないか
- `base/page`
- `db/page`
- `db_exe/page`
- `setting/page`
- `login/page`
- `release/download_zip`

## 典型的な修正パターン

### 1. 未定義キー
- PHP 7 では見逃されていたが、PHP 8 系では warning が出る。

```php
$value = $post["name"];
```

```php
$value = $post["name"] ?? "";
```

```php
$id = $post["id"];
```

```php
$id = (int) ($post["id"] ?? 0);
```

```php
$list = $post["items"];
```

```php
$list = $post["items"] ?? [];
```

### 2. `$_POST` / `$_GET` の直読み
- 可能なら `$ctl->POST("key")`, `$ctl->GET("key")` を優先する。
- 配列丸ごと受けた後に `['key']` で触る場合は個別に `??` を付ける。

```php
$post = $ctl->POST();
$name = $post["name"] ?? "";
```

### 3. null を渡すと deprecated になる関数
- `strtotime(null)`
- `explode(",", null)`
- `preg_match($pattern, null)`
- `mb_strlen(null)`

対策:

```php
$date = strtotime((string) ($post["date"] ?? ""));
$parts = explode(",", (string) ($post["log"] ?? ""));
```

### 4. implicit nullable
- `function x(string $name = null)` は PHP 8.5 で deprecation 対象。

```php
function x(?string $name = null)
```

### 5. セッション値
- ログイン前や CLI 実行では空のことがある。

```php
return $_SESSION[$this->windowcode]["type"] ?? null;
```

## Smarty 移行メモ

### Smarty 4 -> 5 で対応が必要だったもの
- `addPluginsDir()` は将来的に非推奨
- `template_dir`, `compile_dir` 直アクセスは setter/getter に置換
- `SMARTY_PLUGINS_DIR` などの定数は削除されている
- 旧プラグインの `_checkPlugins()` 前提コードは修正が必要
- テンプレート内の `{date("Ymd")}` はそのまま使えない

### テンプレートの置換例

```smarty
{date("Ymd")}
```

```smarty
{$timestamp|date_format:"%Y%m%d"}
```

## `app-framework5` で優先的に洗うクラス
- `fbp/lib/Controller_class.php`
- `fbp/app/db/db.php`
- `fbp/app/db_exe/db_exe.php`
- `fbp/app/constant_array/constant_array.php`
- `fbp/app/cron/cron.php`
- `fbp/app/upload/upload.php`

## 各プロジェクトで優先的に洗う箇所
- `search` / `page` / `edit` / `add_exe` / `edit_exe`
- フォーム検索条件
- CSV download
- PDF 出力
- 公開ページ
- 履歴検索や日付検索

## 典型的に危ない書き方
- `$post = $ctl->POST(); $post["search_xxx"]`
- `$data = $ctl->POST(); $data["id"]`
- `$_POST["nonce"]`
- `explode(",", $post["log"])`
- `strtotime($post["date"])`

## 進め方
1. `~/scripts/check_migration_php7to8.sh <app_root>` を実行する
2. `PHP_NULLABLE`, `PHP_INPUT_ACCESS`, `PHP_LINT`, `RUNTIME` をゼロにする
3. `rg` で直接アクセス箇所を洗う
4. 共通化できるものを framework 側で直す
5. 管理画面の主要クラスを 1 ファイル単位でまとめて直す
6. `php8.5` で `app_call` を実行する
7. `app_call` で見つけた再現可能エラーは checker に入れられるか検討し、固定ルートで再現できるものは `check_migration_php7to8.sh` に追加する
8. warning が消えるまで繰り返す

## checker へ蓄積する基準
- `class`, `function`, `post_array` が固定できる
- ローカル開発環境で毎回再現できる
- 外部サービス依存が薄い
- 実行結果を `Warning`, `Deprecated`, `Fatal error`, `Syntax error in template` で機械判定できる

静的検出として追加しやすいもの:
- `$post["..."]`, `$_POST["..."]`, `$_GET["..."]` の生アクセス
- `??` なし、`isset/empty/array_key_exists` なし、かつ条件式や関数引数で直接使っている危険パターン

追加候補:
- `db/edit` のような固定 ID で再現する管理画面
- `release/download_zip` のようなテンプレートコンパイル系
- `email_format/page` のような一覧描画系

追加しにくい候補:
- 実データや外部 API 状態に強く依存するもの
- ブラウザ操作や JS イベント前提のもの
- 認証トークンや外部 callback を要するもの

## 便利な検索例

```bash
rg -n '\$post\[[^\]]+\]|\$data\[[^\]]+\]|\$_POST\[[^\]]+\]|\$_GET\[[^\]]+\]' fbp/app fbp/lib fbp/interface -g '*.php'
```

```bash
rg -n '\{date\(' . -g '*.tpl'
```

```bash
php8.5 /home/nakama/web/app-framework5/fbp/cli.php app_call --json='{"class":"base","function":"page"}'
```

## 判断基準
- warning を隠すのではなく、原因コードを直す
- `display_errors=Off` で逃げない
- `?? ""`, `?? null`, `?? []` は値の意味に合わせて選ぶ
- ID は `(int)`, フラグは `(int)` or strict compare に寄せる
- `RUNTIME` は確定不具合として最優先で修正する
- `PHP_INPUT_ACCESS` は大半が修正対象と見なしてよい
- 後回しにするのは、直前代入で必ず値が入ると読める少数の箇所だけにする

## 各プロジェクト移行時の使い方
1. `~/scripts/check_migration_php7to8.sh /home/nakama/web/<app-code>` を実行する
2. `TPL_DATE`, `PHP_NULLABLE`, `PHP_LINT`, `RUNTIME` を先に潰す
2. `PHP_NULLABLE`, `PHP_LINT`, `RUNTIME` を先に潰す
3. `PHP_INPUT_ACCESS` を一覧として扱い、同一ファイル内の似たパターンをまとめて修正する
4. 修正後に checker を再実行し、件数が減っていることを確認する
5. 必要なら個別の `app_call` を追加し、再現ルートを checker に戻す

## 完了条件
- `~/scripts/check_migration_php7to8.sh <app_root>` が `OK`
- `app-framework5` 主要画面が `php8.5` で warning/fatal なし
- Smarty 5 で主要テンプレートが通る
- 各プロジェクトは `classes/app` の warning を個別に潰せる状態になっている

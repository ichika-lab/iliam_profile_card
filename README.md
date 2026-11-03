# IRIAM支援アプリ

Laravel 13 + Inertia.js 2 + Vue 3の開発用プロジェクトです。DockerでPHP 8.3、Composer 2.8、Node.js 22、PostgreSQL 16を提供します。ホストへのPHP・Composer・Node.jsのインストールは不要です。Docker Desktop（Compose v2）が必要です。

## 既存環境を使う前の確認

```sh
git status --short
docker version
docker compose ps -a
```

既存の未コミット変更、`.env`、`database/database.sqlite`、PostgreSQLの`pgdata`ボリュームを保護してください。既存のSQLiteを使用している場合、以下の初回手順で`.env`を上書きしないでください。現在のSQLiteはそのまま利用できます。

SQLiteからPostgreSQLへの切り替えはデータ移行ではありません。既存データを移す場合は、件数・バックアップ・移行方法を確認し、所有者の承認を得てから別作業として実施してください。今回、既存DBの移行は行っていません。

## 初回セットアップ

以下は新規のローカル開発環境向けです。本番環境では実行しないでください。

1. `.env`がない場合だけ作成します。

```sh
test -f .env || cp .env.example .env
```

2. イメージを構築し、ロックファイルに従って依存を導入します。

```sh
docker compose build app
docker compose run --rm --no-deps app composer install --no-interaction
docker compose run --rm --no-deps app npm ci
docker compose run --rm --no-deps app composer check-platform-reqs
docker compose run --rm --no-deps app php -v
docker compose run --rm --no-deps app composer -V
docker compose run --rm --no-deps app node --version
docker compose run --rm --no-deps app npm --version
```

依存はホストと共有する`vendor/`・`node_modules/`に配置します。ホストで導入済みのNode依存がある場合も、コンテナ用に`npm ci`を実行します（`node_modules`は再作成されます）。パッケージのバージョンを更新する`composer update`・`npm update`は通常のセットアップには不要です。

3. `.env`の`APP_KEY`が空の場合だけ生成します。既存キーは変更しないでください。

```sh
docker compose run --rm --no-deps app php artisan key:generate
```

4. 新規PostgreSQL環境では、`.env`を次の設定にします。既存SQLite環境は変更不要です。

```dotenv
APP_ENV=local
APP_URL=http://localhost:8000
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=iriam_card
DB_USERNAME=root
DB_PASSWORD=password
```

`DB_URL`が設定されている場合は接続設定に影響するため確認してください。上記パスワードはローカル開発専用です。既存PostgreSQLボリュームでは初期化済みの認証情報が使われ、Composeの値を変えるだけでは変更されません。

5. 新規の開発DBだけを対象に、通常のマイグレーションを明示的に実行します。既存DBの更新は内容を確認してから実行してください。

```sh
docker compose up -d db
docker compose run --rm app php artisan migrate
```

SQLiteでテストする場合もPDO SQLiteを利用できます。既存SQLiteのバックアップなしでDB設定を切り替えないでください。設定キャッシュが残っている場合は、接続先を確認したうえで`php artisan config:clear`をコンテナ内で実行してください。

## LaravelとVueの起動

```sh
docker compose up -d app vite db
docker compose ps
docker compose logs --tail=100 app vite db
```

- アプリ: http://localhost:8000
- Laravelヘルスチェック: http://localhost:8000/up
- Vite: http://localhost:5173（アプリ画面は8000側を開く）

VueはInertia経由でLaravelから表示されます。Viteは別サービスで起動し、HMRは`localhost:5173`を使用します。ポートはホストのループバックだけに公開します。リモートホストから使う場合はURL・HMR・ポート設定を別途見直してください。

起動時にマイグレーション、依存インストール、Seederは実行しません。依存がない場合は先に初回手順を実行してください。`app`は既存設定を維持して自動再起動しません。DBのhealthcheck完了後に起動します。

既存コンテナに構成変更を反映するには再作成が必要です。作業中の開発サーバーを停止するため、利用者と調整して上記コマンドを実行してください。DBボリュームは維持されます。

## DB接続

| 接続元 | ホスト | ポート | DB / ユーザー |
|---|---|---|---|
| Compose内Laravel | `db` | 5432 | `iriam_card` / `root` |
| ホストのDBクライアント | `127.0.0.1` | 5432 | `iriam_card` / `root` |

```sh
docker compose exec db psql -U root -d iriam_card
```

アプリがSQLite設定のままならPostgreSQLコンテナのデータは使用しません。`pgdata`を削除して接続問題を解決しないでください。

## Seeder

Seederは自動実行しません。画面からユーザー登録できます。既存の`DatabaseSeeder`は固定メールのテストユーザーを毎回作成するため再実行に弱く、`AdminUserSeeder`は固定の初期パスワードを使用します。既存DBでは無条件に`db:seed`を実行しないでください。Seederの改善は別作業です。

## 検証と停止

```sh
docker compose run --rm --no-deps app npm run build
docker compose run --rm --no-deps app php artisan test
docker compose stop
```

PHPUnitはメモリ内SQLiteを使用します。カード保存処理と既存プロフィールテストには既知の不整合があり、Phase 0では修正していません。フロント専用のテストは未整備です。

Viteを停止してビルド成果物を確認する場合は`public/hot`が残っていないことを確認してください。これはVite起動中は必要な生成ファイルです。

`docker compose down -v`、`migrate:fresh`、DBファイル削除は使用しないでください。`composer run setup`はキー生成・DB更新を含むため、既存環境ではこのREADMEの個別手順を使ってください。

## AIによる開発

変更前に`git status`を確認し、既存変更とDBデータを保護します。実行コマンドは上記Docker経由で統一します。Laravel 13・Inertia・Vue 3と認証基盤を維持し、依存追加やDB移行は目的を確認してから行います。AGENTS.mdのBoost導入は別途依存追加を伴うため、Phase 0では実施していません。

## Phase 0の確認結果（2026-10-13）

- `docker compose config --quiet`と`docker compose build app`が成功しました。
- 新イメージ内でPHP 8.3.35、Composer 2.8.12、Node.js 22.23.3、npm 10.9.9を確認しました。イメージのタグはパッチ更新を許容するため、次回ビルド時には変わる場合があります。
- ソースを読み取り専用で渡した一時コンテナで、既存vendorのコピーに対する`composer install`、`composer check-platform-reqs`、`npm ci`、`npm run build`を確認しました。
- 一時コピーでLaravelとViteを起動し、`/up`・`/login`・Viteクライアント・アプリのJS配信がHTTP成功することを確認しました。セッションは検証用のメモリ内設定で、ブラウザーのHMR操作は未確認です。
- ローカルPostgreSQL 16.15と、新PHPイメージのPDOから`iriam_card`への読み取り接続を確認しました。既存PostgreSQLには`sessions`テーブルがなく、DBを更新しない検証では通常のDBセッションでログイン画面が500になりました。PostgreSQLでの本運用確認は、承認後のDB準備が必要です。
- PHPUnitは一時コピーのメモリ内SQLiteで23件成功・2件失敗でした。失敗は既存`ProfileTest`の名前・メール更新とカード更新処理の不整合です。
- 稼働中コンテナの再作成、既存DBへのマイグレーション・Seeder、SQLiteからのデータ移行は実行していません。既存`.env`・SQLiteファイルのハッシュは検証前後で一致しました。

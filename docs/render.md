# Render無料プランへの公開

開発用の`Dockerfile`・`compose.yaml`はそのまま使い、公開時だけ`Dockerfile.render`を指定します。Laravel 13 + Inertia + Vue 3を単一Webサービスとして配信します。Vite開発サーバーは公開環境で起動しません。

## 1. GitHubに保存

`git status`と差分を確認し、公開用ファイルをコミット・pushします。`.env`やDB、接続URL、秘密鍵はコミットしません。

```sh
git add Dockerfile.render docker/render docs/render.md bootstrap/app.php README.md .dockerignore
git diff --cached
git commit -m "Render無料プラン向けの公開構成を追加"
git push origin develop
```

ブランチ名は実際に公開するブランチに合わせてください。

## 2. PostgreSQLを用意

Renderで`New → Postgres`を選択し、Webサービスと同じリージョンで作成します。選べる場合はPostgreSQL 16を使用します。`Internal Database URL`を控えてください。

ローカルのSQLiteやPostgreSQLは移行しません。Renderでは別の新規DBを使います。既存DBを接続する場合、起動前にマイグレーション内容・バックアップを確認し、更新の承認を得てください。

無料PostgreSQLは作成30日後に期限切れになります。継続運用や重要データの保存には期限・バックアップ計画が必要です。無料Webサービスのローカルファイルは永続化されないため、SQLiteやアップロード画像の永続保存先には使用しません。

## 3. Web Serviceを設定

| 項目 | 値 |
|---|---|
| Repository | このGitHubリポジトリ |
| Branch | 公開するブランチ（例：develop） |
| Runtime | Docker |
| Instance Type | Free |
| Root Directory | **空欄** |
| Dockerfile Path | `./Dockerfile.render` |
| Docker Build Context | `.`（項目がある場合） |
| Docker Command | 空欄（DockerfileのCMDを使用） |
| Health Check Path | `/up` |

Renderでは`compose.yaml`を起動しません。ApacheはRenderの`PORT`（標準10000）でHTTPを待ち受け、Render側がHTTPSを提供します。

## 4. Environmentを設定

以下はRenderの画面へ登録する値で、ローカルの`.env`を書き換える手順ではありません。

```dotenv
APP_NAME=IRIAM Support
APP_ENV=production
APP_DEBUG=false
APP_URL=https://実際のサービス名.onrender.com
APP_KEY=base64:別途生成した値
DB_CONNECTION=pgsql
DB_URL=RenderのInternal Database URL
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
MAIL_MAILER=log
RUN_MIGRATIONS=true
```

`RENDER=true`はRenderが自動設定します。この値がある場合だけ、LaravelはRenderのプロキシからの転送ヘッダーを信頼します。これによりHTTPSのリンク・セッションを正しく扱います。

`APP_KEY`は次で表示できます。`--show`により既存`.env`は変更されません。表示値を安全にRenderへ登録し、公開後は固定します。

```sh
docker compose run --rm --no-deps app php artisan key:generate --show
```

設定名は`DATABASE_URL`ではなく、このアプリの`config/database.php`が読む**`DB_URL`**です。`MAIL_MAILER=log`ではパスワード再設定メールは配送されません。実際の送信には別途メールサービスの設定が必要です。

## 5. デプロイ

Renderでデプロイを開始します。ビルド中は本番PHP依存のインストールとVueのビルドだけを行い、DBへ接続しません。

起動時は必要な環境変数を確認し、設定キャッシュ生成 → `php artisan migrate --force --no-interaction` → Bladeキャッシュ生成 → Apache起動の順で実行します。マイグレーションに失敗したらHTTPサーバーは起動せず、ログへエラーを出します。Seeder・`migrate:fresh`・自動キー生成は実行しません。

無料プランにはPre-Deploy Commandがないためこの順序です。起動のたびに未適用マイグレーションを適用するため、今後のDB変更もデプロイ前に必ず確認してください。承認済みのDBを別途準備済みの場合は`RUN_MIGRATIONS=false`で自動適用を止められますが、必要なテーブルがなければ画面は動きません。

DB変更はイメージを前の版へ戻しても自動では戻りません。ロールアウト中は旧版と新版が同じDBを使う可能性もあるため、既存コードと互換性のあるマイグレーションにしてください。

## 6. 公開確認

- `/up`：ヘルスチェック
- `/dice`：画面・画像・ダイス操作
- `/register` → `/login` → ログアウト：新規DBでの認証
- ブラウザーのNetwork：JS/CSSがHTTPSで読み込まれ、`localhost:5173`を参照していないこと

プロフィール保存の既知の不整合は、この公開設定では修正していません。無料Webサービスはアイドル時に停止し、次のアクセスで起動に時間がかかることがあります。

## ローカルで公開用イメージをビルド

```sh
docker build -f Dockerfile.render -t iriam-render:local .
```

これはローカルDBへマイグレーションを実行しません。起動検証する場合は、普段のDBではなく破棄可能な専用PostgreSQLへ接続してください。公開・既存DBへの接続はこの手順に含めません。

公式資料：
- https://render.com/docs/docker
- https://render.com/docs/deploys
- https://render.com/docs/free

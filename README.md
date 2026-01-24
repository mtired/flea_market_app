# フリマアプリ

## 環境構築

### Docker ビルド
docker compose(v2)を使用してください

```bash
git clone https://github.com/mtired/flea_market_app.git
docker compose up -d --build
docker compose exec php bash
composer install

cp .env.example .env # .env の環境変数を適宜変更

php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
```

## 開発環境
ユーザー登録画面：
http://localhost/register

ログイン画面：
http://localhost/login

トップ画面：
http://localhost/

phpMyAdmin：
http://localhost:8080/

## 使用技術(実行環境)
Laravel：12.43.1

mysql：8.4

nginx：1.28

php：8.4.15

Composer：2.9.2

## 動作について
■ページ遷移について
・公開ページ
　-トップページ(商品一覧ページ)
　-商品詳細ページ

・ゲストページ（未ログイン）
　-ログインページ
　-登録ページ

・ログイン済み + メール未認証
　-メール認証誘導ページ

・ログイン + メール認証済み + プロフィール未更新
　-プロフィール編集画面

・ログイン + メール認証済み + プロフィール更新済み
　-プロフィールページ
　-プロフィール編集画面
　-商品購入ページ
　-住所変更ページ
　-商品出品ページ

■その他挙動について
・商品詳細ページについて
　-いいね、コメントは未ログインの状態ではできません。ログインページに飛びます。

・プロフィールページについて
　-商品一覧が表示されますが、商品詳細ページに飛ぶことはありません。

## PHP Unitテスト
テストコマンド一覧

```bash


```
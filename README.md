# フリマアプリ

## 環境構築

### Docker ビルド
```bash
git clone https://github.com/mtired/flea_market_app.git
docker-compose up -d --build
docker-compose exec php bash
composer install

cp .env.example .env # .env の環境変数を適宜変更

php artisan key:generate
php artisan migrate
php artisan db:seed
```

## 開発環境
お問い合わせ画面：
http://localhost/

ユーザー登録：
http://localhost/register

ログイン：
http://localhost/login

phpMyAdmin：
http://localhost:8080/

## 使用技術(実行環境)
Laravel：12.43.1

mysql：8.4

nginx：1.28

php：8.4.15

Composer：2.9.2

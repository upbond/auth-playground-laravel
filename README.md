This is a sample project demonstrating how to integrate [the Auth0 Laravel SDK](https://github.com/auth0/laravel-auth0) into a Login 3.0 ecosystem. For Laravel 10 applications, the integration steps are identical.

## Getting Started

Use Composer to install the dependencies and prepare env variables:

```bash
composer install
cp .env.dev .env
```

Run the application:

```
php artisan serve --host localhost --port 8000
```

Open in browser http://localhost:8000

## User Info Related
### Middleware 
Middleware contains the function to get userinfo and verify JWT. Defined on [Userinfo.php](app/Http/Middleware/Userinfo.php)

### APIs
API that defined a /api/userinfo for GET and POST is on [api.php](/routes/api.php)
- Route::get('/userinfo'...
- Route::post('/userinfo'...

### Call userinfo API service
Contain on [Utils.php](app/Http/Helpers/Utils.php)
- `getUserInfoUrl` function is to get userinfo URL from .well-known/openid-configuration
- `getUserInfo` is to get userinfo from userinfo API that related with mysql database

## License

This project is licensed under the MIT license by Auth0. See the [LICENSE](./LICENSE) file for more info.


--------


これは、[Auth0 Laravel SDK](https://github.com/auth0/laravel-auth0)をLogin 3.0に統合する方法を示すサンプルプロジェクトです。Laravel 10アプリケーションの場合、統合手順は同じです。

## はじめに

Composerを使用して依存関係をインストールし、env変数を準備します：

```bash
composer install
cp .env.dev .env
```

アプリケーションを実行する：

```
php artisan serve --host localhost --port 8000
```

ブラウザで開く http://localhost:8000

## ユーザー情報関連
### ミドルウェア 
ミドルウェアには、ユーザー情報を取得し、JWTを検証する機能が含まれている。[Userinfo.php](app/Http/Middleware/Userinfo.php)で定義されています。

### API
GETとPOST用の/api/userinfoを定義したAPIは[api.php](/routes/api.php)にあります。
- Route::get('/userinfo'...
- Route::post('/userinfo'...

### userinfo APIサービスを呼び出す
[Utils.php](app/Http/Helpers/Utils.php)に記述。
- getUserInfoUrl`関数は.well-known/openid-configurationからuserinfoのURLを取得する。
- getUserInfo`関数は、mysqlデータベースに関連するuserinfo APIからユーザ情報を取得します。

## ライセンス

このプロジェクトのライセンスはAuth0によるMITライセンスです。詳細は[LICENSE](./LICENSE)ファイルを参照してください。

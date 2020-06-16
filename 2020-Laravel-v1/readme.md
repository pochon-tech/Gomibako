# Laravel-Sample-1

### Laravel 準備

```sh:
$ docker-compose up -d
$ docker-compose exec php bash
$ composer create-project laravel/laravel=6.* laravel --prefer-dist
$ chmod -R 777 laravel/storage # Storageのアクセス制限
$ ln -s laravel/public/ ./html # ApacheのWebRootDir「var/www/html」に合わせる 
```

### .env書き換え
- DBの接続情報を修正する。

```.env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=user
DB_PASSWORD=password
```

### デフォルト認証機能の生成

```sh:
$ cd laravel
# Auth系生成
$ composer require laravel/ui 1.* 

# laravel 7 の場合↓
# $ composer require laravel/ui å

# 標準のUserMigrationファイルからテーブル生成
$ php artisan migrate

# 画面上にユーザ登録UIとか作成
$ php artisan ui react --auth #  --auth をつけることで、js/views配下にBladeが生成される
$ apt install nodejs npm # node install (debian系)
$ npm install # project内にnode-modulesをInstall
$ npm run dev # build

# 他のUIFWを使う場合、下記を実行すればファイル生成される
# $ php artisan ui vue
# $ php artisan ui bootstrap

# Reactのコンポーネントのexampleを確認してみる
# welcom.blade.phpに下記を追加
<div id="example"></div>
<script src="{{mix('js/app.js')}}" ></script>
$ npm run dev
```


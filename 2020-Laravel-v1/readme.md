# Laravel-Sample-1

### Laravel 準備

```sh:
$ docker-compose up -d
$ docker-compose exec php bash
$ composer create-project laravel/laravel=6.* laravel --prefer-dist
$ chmod -R 777 laravel/storage # Storageのアクセス制限
$ ln -s laravel/public/ ./html # ApacheのWebRootDir「var/www/html」に合わせる 
```

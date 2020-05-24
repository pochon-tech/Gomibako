## 概要

PHPで独自にサーバーを立てる練習。

### Docker-PHP

- php:7.3-apache

### http: 80 

- www/html

### .www/tcp-client-server

- HTTPはまだ使わずにTCPだけで通信
- dotenv install
```sh:
$ docker-compose exec php bash
$ cd tcp-client-server
$ compose init
# PHP dotenv インストール
$ composer require vlucas/phpdotenv
$ vi .env
export HTTP_TCP_SOCK_DEV='localhost:8000'
```
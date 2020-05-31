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

- `stream_socket_server($local_socket, $errno, $errstr)`: local_socketで指定された接続ポイントに、ストリームまたはデータグラムソケットによる接続を作成する。
  - `local_socket`: 作成されるソケットのタイプは、`[トランスポート]://[ターゲット] `形式の URL フォーマットによって指定されたトランスポートによって決定される。
  - コネクションレス型通信(データグラム型通信):
    - 情報を今から伝えるということを相手に知らせずにデータを送信する。相手が受け取る準備ができているかにかかわらず，いきなり送信するので、 コネクションレス型といわれる。
    - この通信では、情報が確かに相手に届くことは保証されず、届いたとしても到着順は保証されない。
    - リアルの生活に例えると、手紙でおこなう通信に似ている。手紙の到着順は保証されないし、届いたかどうかもわからない。信頼性のない通信。
    - UDPはこのデータグラム通信を提供する。
  - コネクション型通信(ストリーム型通信):
    - 送信したデータが通信相手に間違いなく届き、かつデータを送信した順にデータが 受け取られることが保証される。
    - これを実現するために、通信の相手にデータが到着したことを 確認しながら通信を行うので、相手に通信を開始することを、告げて、相手から応答があって、相手が間違いなく存在し、受信できる状態であることを確認してから通信を始めるのでコネクション型通信と呼ばれる。
    - TCPはこのストリーム型通信を提供する。
    - コネクションが確立された時点で通信しようとする ポート に相手が存在することが確認されており、相手が受け取る準備ができていることが保証される。
- `stream_socket_accept($server_socket)`: `stream_socket_server` で作られたソケットの接続を受け入れる。受け付けたソケット接続へのストリームを返す。
- `stream_socket_client($remote_socket, $errno, $errstr, $timeout)`: remote_socket で指定された接続先との、 ストリームまたはデータグラム接続を確立する。
  - `remote_socket`: 接続するソケットのアドレス。

### .www/multi-tcp-client-server

- 引き続き、TCPでの通信
- 同時処理を行えるようにする。
```sh:
$ docker-compose exec php bash
$ cd multi-tcp-client-server
$ composer init
$ vi .env
$ cp ../tcp-client-server/exec.sh exec.sh 
```

- `pcntl_signal($signo, $handler)`: $signo が指すシグナルに関するハンドラを新たに設定するか、既存のハンドラを置き換える。
  - `pcntl_signal`を使用する際は、必ず`declare(ticks = 1);`を定義する必要がある。
  - `signo`: シグナル番号。
  - `handler`: シグナルハンドラ。callableを渡すと、それを実行してシグナルを処理する。シグナルハンドラの引数には必ずシグナル番号が必要。
- `declare`: `declare (命令)`。あるコードブロックの中に 実行ディレクティブ（実行命令）をセットするために使用される。使用できるディレクティブは、`ticks`,`encoding`,`strict_types`。
  - `ticks`: declareブロックの実行中にパーサが N個の低レベル tick 可能な文を実行するごとに 発生するイベントのこと。
    - すべての文が tick 可能なわけではない。たとえば条件式や引数式などは tick できない。



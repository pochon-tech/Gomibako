## Goで何かする。

- 環境
```sh:
$ docker-compose up -d
$ docker-compose exec go sh
$ go version
go version go1.14.4 linux/amd64
```

- 実行
```sh:
$ docker-compose exec go sh
$ go run main.go
# もしくは docker-compose exec Service Command
$ docker-compose exec go go run main.go
```

**HTTPサーバ立てる**
- `net/http`パッケージを使用する。
  - HTTPクライアントとHTTPサーバーを実装するために必要な機能が提供されている。

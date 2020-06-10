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
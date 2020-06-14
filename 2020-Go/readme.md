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

**リクエストを処理する(HandleFunc)**
- `http.HandleFunc`を使用する。

**リクエストを処理する(Handle)**
- `Handle`を使用する。
  - ダッグタイピングを生かした実装方法。
  - `ServeHTTP(w http.ResponseWriter, r *http.Request)`という関数を持った構造体を`http.Handleの第2引数`に渡すことで、リクエストを処理。
  - `http.HandleFunc`と`http.Handle`の違いは、第2引数に指定できる形式が異なる

**POST処理の実装**
- 特定のHTTPメソッドのみに対応したい場合、頑張る。

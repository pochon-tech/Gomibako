// 扱い特殊。main関数を定義することで、エントリポイントとして使用できる
package main

import (
	"fmt"
    "net/http"
)

func main() {
	// 「/hoge」に対して処理を追加する
	http.HandleFunc("/hoge", handler)
    http.ListenAndServe(":8003", nil)
}

func handler(w http.ResponseWriter, r *http.Request) {
	fmt.Fprint(w, "Hello World")
}

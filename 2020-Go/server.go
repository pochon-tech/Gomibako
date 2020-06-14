// 扱い特殊。main関数を定義することで、エントリポイントとして使用できる
package main

import (
	"fmt"
    "net/http"
)

func main() {
	
	// 「/hoge」に対して処理を追加する
	http.HandleFunc("/hoge", handler)

    // DuckTyping的に、ServeHTTP関数があれば良い.
    http.Handle("/fuga", String("Duck Typing!!!"))

    http.ListenAndServe(":8003", nil)
}

// HandleFuncの第二引数の関数
func handler(w http.ResponseWriter, r *http.Request) {
	fmt.Fprint(w, "Hello World")
}

// Stringという名前の型を定義（string型）
type String string

// String に ServeHTTP 関数を追加
func (s String) ServeHTTP(w http.ResponseWriter, r *http.Request) {
    fmt.Fprint(w, s)
}


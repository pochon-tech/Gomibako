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
	
	// POSTメソッドのみ許可する
	http.HandleFunc("/only-post", postHandler)

    // パラメータ受け取り
	http.HandleFunc("/request-params", handleParams)

    // 静的ファイル配信.
    // ディレクトリ名をURLパスに使う場合
    // 例：http://localhost:8080/contents/sample1.txt
    http.Handle("/contents/", http.FileServer(http.Dir("./")))

    // 静的ファイル配信.
    // ディレクトリ名とURLパスを変える場合
    // 例：http://localhost:8080/mysecret/sample1.txt
    http.Handle("/mysecret/", http.StripPrefix("/mysecret/", http.FileServer(http.Dir("./contents"))))


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

// post処理のみkyok
func postHandler(w http.ResponseWriter, r *http.Request) {
	// HTTPメソッドをチェック（POSTのみ許可）
	if r.Method != http.MethodPost {
		w.WriteHeader(http.StatusMethodNotAllowed) // 405
		w.Write([]byte("POST ONLY"))
		return
	}
	w.Write([]byte("OK"))
}

func handleParams(w http.ResponseWriter, r *http.Request) {

    // クエリパラメータ取得してみる
    fmt.Fprintf(w, "query:%s\n", r.URL.RawQuery)

    // Bodyデータを扱う場合には、事前にパースを行う
    r.ParseForm()

    // Formデータを取得. := で var指定いらない
    form := r.PostForm
    fmt.Fprintf(w, "form1:\n%v\n", form)

    // または、クエリパラメータも含めて全部. := で var指定いらない
    params := r.Form
    fmt.Fprintf(w, "form2:\n%v\n", params)
}
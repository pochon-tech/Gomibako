**参考**
- http://sweng.web.fc2.com/ja/program/awk/quick-start.html

**準備**
`docker-compose exec node sh`

**構文**
`$ awk '{ awkのコード }' ファイル名`

**練習**
- ファイル内容出力

```sh:
$ vi sample.dat
1 10 100
2 20 200
3 30 300
# 変数「$0」には、現在処理している一行の内容が格納
$ awk '{ print $0; }' sample.dat
```

- 各行の、各列を出力
```sh:
# $0: 現在処理している1行
# $1,$2...: 処理している1行を「空白で分割」して格納される
# "hoge": ""で括られた文字は文字列として表示
$ awk '{ print $1","$2","$3; }' sample.dat
1,10,100
2,20,200
3,30,300
```

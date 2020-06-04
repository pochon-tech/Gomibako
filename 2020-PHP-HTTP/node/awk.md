**参考**
- http://sweng.web.fc2.com/ja/program/awk/quick-start.html

**準備**
`docker-compose exec node sh`

**構文**
`$ awk '{ awkのコード }' ファイル名`

**練習1**
- 出力

```sh:
$ vi sample.dat
1 10 100
2 20 200
3 30 300
# 変数「$0」には、現在処理している一行の内容が格納
$ awk '{ print $0; }' sample.dat
```
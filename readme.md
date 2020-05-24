
## Dockerデーモンをターミナルから再起動（Mac）

```sh
$ osascript -e 'quit app "Docker"'
```

### シェルを用意する

```sh:
vi ~/docker-deamon-restart.sh
#!/bin/bash
osascript -e 'quit app "Docker"'
open -a /Applications/Docker.app 
# 実行
$ sh ~/docker-deamon-restart.sh
```

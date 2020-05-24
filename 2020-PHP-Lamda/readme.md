## 概要

AWS + Lambda + PHP

## 作業

- TryプロジェクトのClone
```
$ git clone https://github.com/pochon-tech/Try.git
$ cd Try; 2020-PHP-Lamda;
```

- AWS CLIのインストール (まだない場合)
```sh:
# VSCODE上でターミナルを開く（Mac）[control] + [shift] + [^]
# pip3推奨
$ pip3 --version
pip 20.0.2 from /usr/local/lib/python3.7/site-packages/pip (python 3.7)
# Python 3.4 以降が推奨
$ python3 --version
Python 3.7.7
# aws cliのインストール
$ pip3 install awscli --upgrade --user
# --user スイッチを使用すると、pip は AWS CLI を ~/.local/bin にインストールされる？
$ vi ~/.bash_profile
export PATH=~/.local/bin:$PATH
$ source ~/.bash_profile
$ aws --version
zsh: command not found: aws
# ipのバージョンが違うため、 ~/.local/bin が存在していない
$ ls -a ~/.local/
ls: /Users/apple/.local/: No such file or directory
# pip3 show awscliで確認すると実際には、/Users/apple/Library/Python/3.7/lib/python/site-packages の中にあるようだった。
Required-by: 
Files:
  ../../../bin/aws
  ../../../bin/aws.cmd
  ../../../bin/aws_bash_completer
  ../../../bin/aws_completer
  ../../../bin/aws_zsh_completer.sh
# 実行ファイルが、../../../bin/aws となっているので、~/Library/Python/3.7/bin/aws
$ vi ~/.bash_profile
export PATH=~/Library/Python/3.7/bin:$PATH
$ source ~/.bash_profile
$ aws --version
aws-cli/1.18.66 Python/3.7.7 Darwin/19.4.0 botocore/1.16.16
```
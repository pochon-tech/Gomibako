# Laravel-Sample-1

### etc git
```sh:
# contriのため
git config --global user.name ''
git config --global user.email ''
# 更新
brew list | grep git # ないこと確認
brew info git | grep stable
brew install git
brew list | grep git # あること確認
# PUSHすると、credential-osxkeychain発生: windowsとは異なり、資格情報は別途保存する必要がありそうだ。
# 初めてのPUSH時はGithubのUSERとPASSを入力

# 過去のコミットのAuthorとCommiterの変更
$ git filter-branch -f --env-filter "GIT_AUTHOR_NAME=''; GIT_AUTHOR_EMAIL=''; GIT_COMMITTER_NAME=''; GIT_COMMITTER_EMAIL='';" HEAD 
# すでにプッシュしてしまっているなら、-f が必要になる
$ git push -f origin
```

### Laravel 準備

```sh:
$ docker-compose up -d
$ docker-compose exec php bash
$ composer create-project laravel/laravel=6.* laravel --prefer-dist
$ chmod -R 777 laravel/storage # Storageのアクセス制限
$ ln -s laravel/public/ ./html # ApacheのWebRootDir「var/www/html」に合わせる 
```

### .env書き換え
- DBの接続情報を修正する。

```.env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=user
DB_PASSWORD=password
```

### デフォルト認証機能の生成

```sh:
$ cd laravel
# Auth系生成
$ composer require laravel/ui 1.* 

# laravel 7 の場合↓
# $ composer require laravel/ui å

# 標準のUserMigrationファイルからテーブル生成
$ php artisan migrate

# 画面上にユーザ登録UIとか作成
$ php artisan ui react --auth #  --auth をつけることで、js/views配下にBladeが生成される
$ apt install nodejs npm # node install (debian系)
$ npm install # project内にnode-modulesをInstall
$ npm run dev # build

# 他のUIFWを使う場合、下記を実行すればファイル生成される
# $ php artisan ui vue
# $ php artisan ui bootstrap

# Reactのコンポーネントのexampleを確認してみる
# welcom.blade.phpに下記を追加
<div id="example"></div>
<script src="{{mix('js/app.js')}}" ></script>
$ npm run dev
```

### phpDocumentorのインストール
- `composer require --dev phpdocumentor/phpdocumentor` だとうまくいかない。
- pharを使う
```sh:
apt-get install wget
cd vendor/bin
wget http://phpdoc.org/phpDocumentor.phar
```
- 【使用方法】
- ディレクトリ単位：`php vendor/bin/phpDocumentor.phar -d app -t public/phpdoc`
- ファイル単位：`php vendor/bin/phpDocumentor.phar -f app/Http/Controllers/UserController.php -t public/phpdoc`

### ユーザ機能をDDD風にする

- **コントローラー**
- リクエストを受け取りドメインサービスに投げる。
- バリデーションはフォームリクエストに委ねる
- 雛形の作成：`php artisan make:controller コントローラ名`
- 画面繊維は、一覧画面 → 登録画面 → 一覧画面 を想定

- **入力データのバリデート**
- 値オブジェクトとしてのふるまいにするか、LaravelWayなフォームリクエストで確認するかで悩んだ。
- FormRequest: ValidationをControllerのMethodから切り離し、Validation専用のファイルを作り処理をさせる。
- 通常ControllerのStoreメソッド等でValidationを行ったりするが、肥大化を防止できる
- `php artisan make:request ファイル名`で雛形を生成可能
- 小技として、文字数許可範囲はValueObjectを参照させる。

- **ValueObject**
- ユーザの属性を定義。
- エンティティの生成時に誤設定を回避。
- interfaceの作成: 
  - `mkdir app/ValueObjects; touch app/ValueObjects/BaseValueObject.php;`
- 実装:
  - `mkdir app/ValueObjects/User`
  - `touch app/ValueObjects/User/Email.php`
  - `touch app/ValueObjects/User/Name.php`
  - `touch app/ValueObjects/User/Password.php`

### Tips

<details><summary>サービスコンテナ復習</summary>

**サービスコンテナとは**
- サービスコンテナ = **インスタンスを自動的に生成してくれる**(new の機能を拡張したようなもの)

**サービスコンテナを利用するには**
- サービス化したいクラスを作る
- (サービスプロバイダを作ってapp.phpに登録)
- (registerにインスタンス化する方法を定義)
- (bootでサービス同士の依存解決)
- (ファサード定義してapp.phpに登録)
- サービスコンテナにインスタンスを提供してもらう
- ()は省略可能

**サービスプロバイダ**
- サービスコンテナとは**無関係**
- 名前が似ているだけ

**サービスコンテナ**
- クラスのインスタンスを生成・預かってくれる

**サービスコンテナにクラスのインスタンスを作ってもらうには**
- 一般的なPHPの場合
  - `$sampleobject = new App\SampleClass;`
- Laravelのサービスコンテナの場合
  - `$sampleobject = app()->make('App\SampleClass');`
- app()->make()はどこでも使用でき、このコードを使用するための準備は不要。
  - app()はグローバル関数として定義されているので、Laravelアプリならクラスの中でも外でも、いつでもどこでも呼ぶことができる。
  - 呼ぶと、サービスコンテナが出てくる。つまり、app() = サービスコンテナ。
  - 準備もいらない。サービスプロバイダに登録するとかもいらない。クラスがきちんと定義(new でインスタンスできる状態)されているならすぐ実行できる。
  - (new SampleClass) でもよいし app()->make(SampleClass) でもよい。
  - 他にも書き方がある。
  ```php:
    $sampleobject = app()->make('App\SampleClass'); // コンテナがインスタンスを作る
    $sampleobject = Illuminate\Container\Container::getInstance()->make('App\SampleClass'); // コンテナを取ってきてインスタンスを作る
    $sampleobject = app()->resolve('App\SampleClass'); // コンテナがクラスの依存を解決する
    $sampleobject = resolve('App\SampleClass'); // クラスの依存を解決する
    $sampleobject = app('App\SampleClass'); // インスタンス
    $sampleobject = Illuminate\Foundation\Application::getInstance()->make('App\SampleClass'); // アプリ本体を取ってきてインスタンスを作る
  ```
  - 上記からわかるように、Laravel = サービスコンテナ
</details>

<details><summary>サービスプロバイダ復習</summary>

**サービスプロバイダとは**
- サービスコンテナ = **インスタンス化をする方法を定義する場所**

**サービスコンテナの内部の動き**
- クラスのコンストラクタに引数がある場合
  - クラスのコンストラクタ引数をチェックする。
  - 引数のタイプヒントにクラスが指定されていると、そのクラスをサービスコンテナで再帰的に生成する。
  - 生成されたインスタンスをコンストラクタに渡して、クラスをnewする。
- クラスのコンストラクタに引数がない場合
  - クラスのコンストラクタ引数をチェックする。
  - クラスをnewする。

**結合**
- 「インスタンスの生成方法」をカスタマイズする仕組み

**シングルトン**
- `app()->singleton('App\SampleClass');`
- 上記の記述は、**「App\SampleClassを生成するときはシングルトンとして生成」**という指示。
- 上記の記述により、サービスコンテナは**最初の app('App\SampleClass') で生成されたインスタンスをストックしておき、2回目以降はそのインスタンスを渡す**ようになる。
- つまり、アプリケーションの中で`app('App\SampleClass')`を何回実行しても、生成されるインスタンスは絶対に１つ。
- では、`app()->singleton('App\SampleClass');`の1行はどこで書くのか？ 
- 正解は、どこでも書ける。一番最初の`app('App\SampleClass')`の直前でも良い。おすすめの場所は「サービスプロバイダ」。
- 「サービスプロバイダ」の場所は、`app/Providers/AppServiceProvider.php`とか。
- `AppServiceProvider`の`register`メソッドに下記のような記述を行う。
```php:
    public function register()
    {
        $this->app->singleton('App\SampleClass'); // $this-> ではなく app()->singleton としても同じこと（$this->のほうがちょっとだけ早い）
    }
```

**サービスプロバイダとは(まとめ)**
- 「クラスインスタンスを初期化するための場所」
- なぜ必要なのか？
  - 「特定のクラスが使用する値の初期化」「特定のクラスが使用する別のクラスの初期化」などは、通常クラス変数やクラスのコンストラクタに定義していた。
  - しかし、クラスの環境依存度が高まってしまい、移管、移植、変更、テストが難しくなってしまう。
  - それを解決するため。
- 実行環境によって変わる初期値(BASEドメイン, DBサーバーのIPなど): .env
- アプリケーション実行中はどこでも不変な定数(都道府県IDなど): /config/xxx.php
- 特定のクラスが使う定数(外部APIのURLやTOKEN): .envに書いてサービスプロバイダ
- 特定のクラスが使う別クラスインスタンス(HTTPモジュールなど): サービスプロバイダ
- アプリケーション全体で使うインスタンス: サービスプロバイダ

</details>

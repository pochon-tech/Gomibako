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

**サービス**
- リポジトリへのアクセスやビジネスロジックのやりとりを記述
- 実装
  - `mkdir app/Services; touch app/Services/UserService.php`

**リポジトリ**
- ファクトリやモデルへのアクセスに利用し、各サービスからのモデルアクセス方法を統一
- 実装
  - `mkdir app/Repositories; touch app/Repositories/UserRepository.php`

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

<details><summary>結合復習</summary>

**結合とは**
- 基本形: `app()->bind( $abstract, $concrete );`
- 記述する場所はどこでも問題ないが、`AppServiceProvider`が無難。
- `AppServiceProvider`の`boot`メソッドでも`register`メソッドでもどちらでも問題ない。
  - `boot`と`register`の違いは実行されるタイミング
  - 先に`register`メソッドが実行される。すべてのServiceProviderのregisterが終わった後に、`boot`メソッドが実行される。
  - 使い分けとしては、**他のServiceのインスタンスを使いたい**場合は、`boot`、**独立した初期化**の場合は`register`に記述する。
- `$abstract`, `$concrete` がわかりづらいが要は、`$label`, `$service`という感じ。
  - $abstract: 抽象。ラベルである、サービスコンテナに入れるものにはそれぞれラベルを貼り付ける。ラベルなので文字列。ユニーク。
  ```php:
    // 預ける Labelはなんでも良い。
    app()->bind('date', $concrete1);
    app()->bind('App\User', $concrete2);
    app()->bind( MyClass::class, $concrete3); // ::class で完全なクラス名を取得する (https://stackoverflow.com/questions/35378270/myclassclass-get-string-representation-of-myclass)
    // 取り出す
    app()->bind('date'); // concrete1
    app()->bind('App\User'); // concrete2
    app()->bind(MyClass::class); // concrete3
  ```
  - $concrete: 具象。文字列（クラス名）かクロージャ。
  - クラス名が指定されていると、サービスコンテナはそのクラスをnewして、インスタンスを返す。
  ```php:
    // 預ける
    app()->bind('StdClass', 'StdClass');
    // 取り出す
    $a = app('StdClass');
    var_dump($a);
    //>>> class stdClass#2911 (0) {
    //>>> }
  ```
  - ちなみに、取り出すたびにインスタンスは新しく生成される。シングルトンではない。
  ```php:
    app()->bind('StdClass', 'StdClass');
    $a = app('StdClass'); spl_object_hash($a);
    //>>> "000000007dc70f4a0000000054e8bfb6"
    $a = app('StdClass'); spl_object_hash($a);
    //>>> "000000007dc70f7e0000000054e8bfb6"
  ```
  - さらに、具象自体は省略可能である。その場合、抽象と同じ文字列名が使用される。
  ```php:
    // 2つとも同じ。
    app()->bind('StdClass', 'StdClass');
    app()->bind('StdClass');
  ```
  - シングルトンの表現方法を振り返る。
  ```php:
      // 2つとも同じ。
    app()->singleton('App\MyClass');
    app()->singleton('App\MyClass','App\MyClass');
  ```
  - さらに、シングルトンはbindで置き換えることが可能
  ```php:
    app()->singleton('App\MyClass','App\MyClass');
    app()->bind     ('App\MyClass','App\MyClass', true);
  ```
  - bindの第3引数のshareモード。「newするのは最初の1回だけで、次からは生成済みのインスタンスを返す」
  ```php:
    app()->bind('std','stdClass',true);

    $s = app('std'); spl_object_hash($s); //>>> "000000002fa3c3390000000018649e8a"
    $s = app('std'); spl_object_hash($s); //>>> "000000002fa3c3390000000018649e8a"

    // シングルトンなので、オブジェクトを変更して、別のところで再取得しても反映されている。
    $s->sample = "sample";
    $s2 = app('std'); 
    echo $s2->sample; //>>> "sample"
  ```
  - 具象をクロージャで定義する場合、返せるものだったら何でも良い。
  ```php:
    app()->bind( 'DateTime', function(){ return new DateTime; } );
    echo app( 'DateTime' )->format('Y-m-d'); //>>> 2019-03-20
    // より具体的に実装しても問題ない
    app()->bind( 'birthday', function(){ return new DateTime('1991/04/29'); } );
    echo app( 'birthday' )->format('Y-m-d'); //>>> 1991-04-29
    app()->bind( 'fourty-two', function(){ return 42; } );
    echo app( 'fourty-two' ); //>>> 42
    // 生成済のインスタンスを預けてみる
    $instance = new DateTime('2019/02/10');
    app()->bind( 'some-date', function() use ($instance) { return $instance; } );
    echo app( 'some-date' )->format('Y-m-d'); //>>> 2019-02-10
  ```

**まとめ**
```php:
// 入れる
app()->bind( $label, 'ClassName' );
app()->bind( $label, function(){ return $anything; } );

// 出す
app( $label );
```
</details>

<details><summary>結合の使い所</summary>

**固定値で初期化を行う (よく見かける)**
- 例えばRedisのクライアントクラス。
- サーバ情報等の特定値を初期設定したいとき。初期化するクラスにその設定値を指定してシングルトンとして登録する。
- ちなみに、特定値は.envではなくconfig()ヘルパーを使うようにすると良い。
  - https://github.com/laravel/framework/blob/197a7c3b86d24b8698c61107263b68cb737d51c8/src/Illuminate/Foundation/Bootstrap/LoadEnvironmentVariables.php#L12-L31
  - .envファイルを読み込む箇所のソースコードを確認すればわかるが、**`.env`ファイルの読み込みは、`php artisan config:cache`をしていない場合にしか読み込まれない**
  - キャッシュを有効にしている場合、.envに書いているだけで、「シェルから起動する時点で定義されていない環境変数はすべて未定義」になってしまう。
```php:
// AppServiceProvider.php
$this->app()->singleton(\Predis\Client::class, function(app){
    return new \Predis\Client(
        [
            'scheme' => config('database.redis.default.scheme', 'tcp'),
            'host'   => config('database.redis.default.host', '127.0.0.1'),
            'port'   => config('database.redis.default.port', 6379),
        ],
        [
            'parameters' => [
                'password' => config('database.redis.default.password', null),
                'database' => config('database.redis.default.database', 0),
            ],
        ]
    )
});

// 上記を使用する箇所
$client = app(\Predis\Client::class);
```

**インタフェースから実装クラス**
- 例えば、`laravel/bootstrap/app.php`でも使用しているパターン。
- 一般的に、クラスはインタフェースと分けて実装したほうがよい。**機能を使いたいクラスからは提供側のinterfaceを参照する**のが望ましい。
- 結合を定義しておくことで、実際に動かすときに、そのインターフェースを実装したクラスを注入する
```php:
// AppServiceProvider.php
$this->app->singleton(\App\ProductInterface::class, \App\Product::class);
$this->app->bind(\App\SearchConditionInterface::class, \App\SearchCondition::class);

// 機能を使う側のクラスの書き方
class MyClass 
{
  // コンストラクタインジェクション
  public function __construct( \App\ProductInterface $productService )
  {
      $productService->... // \App\Product のインスタンスが注入される
```

**場合によって初期化する**
- クラスのインスタンスは必ずnewから始まるわけではない。
- 例えば、セッションやファイルにシリアライズされて保存されたインスタンスがある場合、それを使い、ない場合はnewといった感じ。
```php:
// AppServiceProvider.php
// セッションに保存されていたシングルトンを復旧
$this->app->singleton(\App\SessionStore::class, function ($app) {
    // キーが存在していない場合に返すデフォルト値を第2引数に指定できる
    $obj = session('_SESSION_STORE', null);
    return $obj ?? new \App\SessionStore();
});
```
</details>

<details><summary>シングルトンの誤解</summary>

- 当然だが、下記のようなsessionのように画面を跨いで同じインスタンスを引き継ぐことはできない。
```php:
// AppServiceProvider.php
public function register() {
    $this->app->singleton(OrgService::class,OrgService::class);
}
// シングルトンなOrgServiceをOrgControllerで使用する。
class OrgController extends Controller{
    public function input(Request $request){
        spl_object_hash(resolve(OrgService::class);　#・・・A
        spl_object_hash(resolve(OrgService::class);　#・・・B ※Aと同じ識別子を確認
    }

    public function confirm(Request $request){
        spl_object_hash(resolve(OrgService::class);　#・・・C ※A,Bと異なる識別子を確認
    }
}
```
- これはそもそもリクエストのタイミングでPHPプロセスが異なっている。
```sh:
(間違い)
                                        OrgController@input
    Request -> PHPプロセス-> Laravel ->                         -> singleton
                                        OrgController@confirm
(正解)
Request -> PHPプロセス -> Laravel ->  OrgController@input -> singleton
Request -> PHPプロセス -> Laravel ->  OrgController@confirm -> singleton
```
- シングルトンは1回のPHPセッションでしか共有されない。コントローラも同じ。シングルトンもコントローラもリクエスト毎に毎回作り直される。前のことは覚えていない。
- ただし、シングルトンごとsessionデータに保存する方法を使えばSession経由になるので、維持させることは可能。

</details>
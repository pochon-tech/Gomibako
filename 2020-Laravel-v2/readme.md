# Laravel-Sample-2

- 2020/08/21 ~
- Laravelの復習をする。
- Laravelは7系を使用する。
- DDDとか考えずに、単純にLaravelに慣れることが目的。

# お問い合わせフォームを作ってみる

### 要件
- 「名前」「電話番号」「メールアドレス」「お問い合わせ内容」「添付ファイル」「同意チェック」の項目を設ける。
- フロントはLaravelのBladeでまず作る。

### 環境を作る
- `docker-compose up -d` でコンテナ起動
- `docker-compose exec php bash` でPHPコンテナに入る
- `composer create-project --prefer-dist laravel/laravel laravel` でLaravel7系をインストール
- `ln -s laravel/public/ ./html` でドキュメントルートにHTMLをマッピング 
- `http://localhost` で画面へアクセス 
- `cd laravel; php artisan --version;` で LaravelのVersionを確認（7.25.0)
- .envを書き換えて、LaravelからMysqlコンテナの接続を可能にする

### コントローラ、モデル、マイグレーション、シーダーを作成する
- `docker-compose exec php bash` でPHPコンテナに入る
- `php artisan make:controller ContactController -r` でコントローラ (リソース付き) を作成する
- `php artisan make:model Contact -m` でモデルとマイグレーションファイルを作成する
- `laravel/routes/web.php`に`Route::resource('contacts', 'ContactController');` を定義してルーティングを設定する (`php artisan route:list` で確認する)
- `php artisan make:seeder ContactTableSeeder`

### マイグレーションファイルにテーブル定義を記述する
- 主キーのIDについて。7系の`$table->id();` は `Alias of $table->bigIncrements('id')` つまり、符号なしBIGINTを使用した自動増分ID（主キー）がデフォになる
  - [7系の利用できるカラムリスト](https://readouble.com/laravel/7.x/ja/migrations.html#columns)
  - [INTとBIGINTの違い](https://qiita.com/fuubit/items/17f3eb306c64ede163d2)
  - INTは21億までの値を格納できるので、それ以上格納する想定のカラムならBIGINTがよい。
- ひとまず以下のような定義を検討
```php:
    Schema::create('contacts', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('tel');
        $table->string('mail')->unique();
        $table->text('contents');
        $table->string('file_path');
        $table->timestamps();
    });
```
- `php artisan migrate` を実行して テーブルを作成する
- `database/seeds/DatabaseSeeder.php`の編集して、`php artisan db:seed` を実行してテストデータ投入
- `php artisan tinker; $data = App\Contact::all(); exit;` でテストデータを確認

### 一覧表示の実装をしてみる
```php:
    public function index()
    {
        $data = Contact::all();
        return $data; // ... 1
        // return json_encode($data, JSON_UNESCAPED_UNICODE); // ... 2 エスケープ回避
    }
```
- `http://localhost/contacts` にアクセスすると、まだビューを用意していないため、JSONデータが返ってくることがわかる
  - `[{"id":1,"name":"\u7530\u4e2d\u592a\u90ce","tel":"01100001111","mail":"test@exammple.com","contents":"\u304a\u554f\u3044\u5408\u308f\u305b\u3092\u3057\u307e\u3059","file_path":"","created_at":null,"updated_at":null}]`
  - 上記のように $data をそのまま返却すると、JSONエンコード整形された形で返却するようだ。エスケープを回避して画面で一度確認したいのであれば、2 のやり方を行えば良い
  - [PHPにおけるJSONエンコード整形](https://qiita.com/kiyc/items/afb51bce546af3e18594)

### 一覧表示画面を作成してみる
- `resources/views/contacts/layout.blade.php` として基本的なレイアウトファイルを作成する
- `resources/views/contacts/index.blade.php` として一覧表示画面を作成する
- ContactController::index() の返却値を`return view('contacts.index', ['contacts' => $data]);` としてテンプレートを指すようにする

### 詳細表示の実装をしてみる
```html:
  <form action="{{ route('contacts.destroy',$contact->id) }}" method="POST">
      <a class="btn btn-info" href="{{ route('contacts.show',$contact->id) }}">Show</a>
      <a class="btn btn-primary" href="{{ route('contacts.edit',$contact->id) }}">Edit</a>
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-danger" onclick="return window.confirm('Delete ??');">
          <span>Delete</span>
      </button>
  </form>
```
- 上記のように、一覧画面内に詳細画面へ遷移する導線を用意した
- `resources/views/contacts/show.blade.php` として詳細表示画面を作成する
- blade内で、`{{ route('route-name', params) }}`とすることで、route名にしたがってURLを構築してくれる (`php artisan route:list` でroute名は確認できる)
```php:
    public function show(Contact $contact)
    {
        dump($contact->name); // 田中四郎
        return view('contacts.show',compact('contact'));
    }
```
- 上記はControllerの詳細表示の実装である
- [モデル結合ルート](https://readouble.com/laravel/7.x/ja/routing.html#route-model-binding)を使用した実装方法であり、リクエストされたURIの対応する値に一致するIDを持つ、モデルインスタンスを自動的に注入している
- つまり、`http://localhost/contacts/4` のようなURIがリクエストされた場合、contactsテーブルからIDが4のデータを取得するクエリが自動的に実行されて、showメソッドの引数に受け渡されているということになる


### ページング処理を実装してみる
- テストデータを数件、シーダーから登録してみる
- ContactTableSeederにデータを追加して、`php artisan migrate:refresh --seed` を実行してデータベースを再構築する
- ContactController::index() を下記のように変更
```php:
  $data = Contact::latest('id')->paginate(3);
  return view('contacts.index', ['contacts' => $data])
      ->with('i', (request()->input('page', 1) - 1) * 3);
```
- Laravelでは、Controller側で、クエリビルダの`paginate()`を実行して、Blade側で `{{$datas->links()}}` を指定するだけでページングは実装できてしまう。
  - [Laravelでのページング処理](https://www.ravness.com/2018/09/laravelpagination/)
  - [クエリビルダ](https://readouble.com/laravel/7.x/ja/queries.html)のlatest()とpaginate()を使用して、DBからのデータを取得
  - latest() メソッドにより、降順にソートされたデータが取得される (引数を指定することでソートしたいカラムを指定できる、指定しない場合は`created_at`)
  - `{{$datas->links()}}`は、 ページ番号ボタンの実装であり、ページ番号押下時に Getパラメーターに`page=x`を付与させる
    - なお、通常、blade内の `{{ }}文` の記述は、 XSS攻撃を防ぐために、自動的にPHPのhtmlspecialchars関数を通している
    - エスケープをしたくない場合は、`{!! !!}文` に置き換えることで、エスケープ回避できる
- `->with('i', (request()->input('page', 1) - 1) * 3);` は何をしているかというと、一覧表示画面での「No」の変数を現在のページ数によって初期値を変えている実装である
```php:
  $contacts = Contact::latest('id')->paginate(3);
  return view('contacts.index', compact('contacts'))
      ->with('i', (request()->input('page', 1) - 1) * 3);
```
- [ControllerからViewへの変数の受け渡し](https://qiita.com/ryo2132/items/63ced19601b3fa30e6de)を少し変えてみた
- ControllerからViewへ変数を渡す場合は、compact関数を使用するか、withメソッドを使用するかのどちらか
- compact関数のほうが可読性は高い

### マイグレーションファイルでカラムを更新してみる
- 続いて、登録処理を行いたいが、その前に、登録処理をテストするにあたり、添付ファイルはひとまずNULLで登録できるようにしておきたい
- しかし、最初にマイグレーションファイルを作成したときに、NULLを許可しないような定義にしてしまった
- [マイグレーションファイル内でカラムの変更](https://laravel.com/docs/7.x/migrations#modifying-columns)
- マイグレーションファイルで管理するために下記のような手順でカラムの更新を行っていく
  - `php artisan make:migration update_contacts_table --table=contacts`で新しくファイルを作成する
  - 今回は、file_pathのカラム属性をnull許可にしたい
  - ```php:
          public function up()
          {
              Schema::table('contacts', function (Blueprint $table) {
                  $table->text('file_path')->nullable()->change();
              });
              
          }
          public function down()
          {
            Schema::table('contacts', function (Blueprint $table) {
                $table->text('file_path')->nullable(false)->change();
            });
          } 
      ```
  - 定義できたら`php artisan migrate`を実行して更新するが、、、おそらく下記のようなエラーが発生するはず
  - `Changing columns for table "contacts" requires Doctrine DBAL. Please install the doctrine/dbal package.`
  - [カラムの属性を変更する](https://readouble.com/laravel/7.x/ja/migrations.html#modifying-columns)
  - `cd laravel; composer require doctrine/dbal` で該当パッケージをインストールしておく
  - インストールできたら、再び`php artisan migrate`を実行して更新してみる

### 登録処理を実装してみる
- `resources/views/contacts/create.blade.php` : 登録画面
- [LaravelのORMで登録するときのやり方](https://qiita.com/henriquebremenkanp/items/cd13944b0281297217a9#%E4%BD%9C%E6%88%90%E3%81%99%E3%82%8B%E3%81%A8%E3%81%8D)を参考に実装してみる
```php:
    public function create()
    {
        return view('contacts.create');
    }
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'mail' => 'required',
                'tel' => 'required|max:15|not_regex:/[^0-9]/',
            ],
            [
                'name.required' => '名前は必須です',
                'mail.required' => 'メールは必須です',
                'tel.required' => '電話番号は必須です',
                'tel.max' => '電話番号は最大15文字までです',
                'tel.not_regex' => '電話番号は半角数字で入力してください',
            ]
        );
  
        Contact::create($request->all());
        return redirect()->route('contacts.index')->with('success','登録完了');
    }
```
- createメソッドでは、登録フォーム画面を返してあげる
- storeメソッドでは、実際の登録処理とバリデーション処理を実装している
- `$request->validate`の第二引数にカスタムメッセージを指定できる
- これだけでは、まだ登録できないので、Contactモデルに`fillable`を指定して、データベースに保存するカラムを決める
  - `protected $fillable = ['name', 'mail', 'tel', 'contents', 'file_path'];`
  - [fillable使い所](https://qiita.com/mmmmmmanta/items/74b6891493f587cc6599#%E3%81%A9%E3%81%A1%E3%82%89%E3%81%8C%E3%81%8A%E3%81%99%E3%81%99%E3%82%81%E3%81%8B)

### 重複チェックをしてみた
```php:
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'mail' => 'required|unique:contacts,mail',
                'tel' => 'required|max:15|not_regex:/[^0-9]/',
            ],
```
- `unique:[table-name],[colmun-name]`でユニーク制約のかかったカラムの重複チェックを行うことができる
- ただし、例えば自分自身の更新でメール以外の変更はあれど、メールの変更がない場合の除外をしたい場合、Ruleクラスを使うと楽に実装できるようなので、Validationの部分の実装はまだ変更の余地がある
- [ユニークなValidation](https://readouble.com/laravel/7.x/ja/validation.html#rule-unique)


### 編集処理を実装してみる
- `resources/views/contacts/edit.blade.php` : 編集画面
- 詳細表示処理と同じように、モデル結合ルートを使用して、URLのIDに対応したContact情報を編集画面は渡してあげる
- `edit.blade.php` のformタグ内では忘れずに`@method="PUT"`を定義しておく
  - HTMLフォームでは、PUT、PATCH、DELETEリクエストを作成できないため、見かけ上のHTTP動詞を指定するための_methodフィールドを追加する必要がある
  - bladeでは、@methodを使用することで、実現できる

# 参考サイト
- [MarkDown記法](https://notepm.jp/help/how-to-markdown)
- [VSCODEショートカット](https://qiita.com/naru0504/items/99495c4482cd158ddca8)
- [Laravel命名規則](https://qiita.com/gone0021/items/e248c8b0ed3a9e6dbdee)
- [Laravelベストプラクティス](https://webty.jp/staffblog/production/post-1835/)

# VSCODE拡張
- PHP Intelephense: PHPのコード補完、参照の検索や定義への移動などなど
- Dot ENV: .envファイルの色分けしてくれる
- [Laravel関係の拡張リスト](https://qiita.com/hitotch/items/9b5c8e28f50e0e3f7806)

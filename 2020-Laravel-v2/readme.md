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
- Controllerのupdate()メソッドでも、モデル結合ルートを使用して記述量を減らした

### 削除処理を実装してみる
- 特にテンプレートは用意せずに、一覧画面で削除ボタンを押下したら削除されるように実装する
- Controllerのdestroyメソッドもモデル結合ルートを使用して、記述量を減らす
```php:
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success','削除完了しました');
    }
```

### 直前のデータを取得する
- 登録画面で入力エラーでフォームに戻ったときに入力していた値を表示させておきたい
- [直前のデータを取得](https://readouble.com/laravel/7.x/ja/requests.html#old-input)を参考にすると良い
- create.blade.phpのInput要素に`value="{{ old('input-name') }}"` を追加してあげるだけで実現可能

### 直前のデータを取得する （ デフォルトあり )
- 編集画面では、一度登録した内容を取得してViewに返してで表示し、それが変更されたらDBを更新するという流れである
- では、取得した内容をInput要素に入れこみつつ、Validationでフォームに戻ったらold()の内容を再表示する場合はどうすればよいか
- [oldメソッドの第二引数にdefault値を代入](https://tacosvilledge.hatenablog.com/entry/2018/05/14/195402)することで解決することができる
- edit.blade.phpのInput要素に`value="{{ old('name', $contact->name) }}"`といった具合で実現可能

### FormRequestを使用する
- ValidationをControllerから切り離して、Validation専用のファイルを作り処理させてしまおう
- FormRequestで別ファイル化する前に、少しValidation周りで整理する
- 本来、Validationの処理は以下のような記述をControllerに記述する
```php:
    public function store( Request $request ){

        // バリデーションルールを設定
        $validator = Validator::make($request->all(), [
                'name' => 'required',
                'mail' => 'required|unique:contacts,mail',
                'tel' => 'required|max:15|not_regex:/[^0-9]/',
                'contents' => 'required',
        ]);
        // バリデーションルールにでエラーの場合 
        if ($validator->fails()) return redirect('/')->withInput()->withErrors($validator);
    }
```
- FormRequestを使用すると`if ($validator->fails())`文のエラーがある場合の記述が必要なくなるようだ
  - redirect先はフォーム送信元に自動で戻る
  - withErrors()は自動で行われ、$errors変数が作成される
  - withInput()は自動で行われる
- ちなみに、現在のValidation実装は以下
```php:
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'mail' => 'required|unique:contacts,mail',
                'tel' => 'required|max:15|not_regex:/[^0-9]/',
                'contents' => 'required',
            ]
        );
        Contact::create($request->all());
        return redirect()->route('contacts.index')->with('success','登録完了');
    }
```
- すでに、`if ($validator->fails())`文周りは省略されてはいるのだが、Controllerのメソッド毎に記述するのはよろしくない
- ここからが本題だが、`php artisan make:request ContactInputPost` を実行して、FormRequestを継承したClassを作成し、以下のようにルールとカスタムメッセージを実装する
```php:
    public function authorize()
    {
        return true; // 使用する場合は、falseからtrueに変更（デフォルトはfalse)
    }
    // Validationルール
    public function rules()
    {
        return [
            'name' => 'required',
            'mail' => 'required|unique:contacts,mail',
            'tel' => 'required|max:15|not_regex:/[^0-9]/',
            'contents' => 'required',
        ];
    }
    // カスタムメッセージ省略可能 function名は必ず「messages」
    public function messages(){
        return [
            'name.required' => '名前は必須です',
            'mail.required' => 'メールは必須です',
            'tel.required' => '電話番号は必須です',
            'tel.max' => '電話番号は最大15文字までです',
            'tel.not_regex' => '電話番号は半角数字で入力してください',
        ];
    }
```
- 上記のファイルを用意できたら、Controllerに記述していたValidation処理を削除する
- また、メソッドの引数を下記のように変更して、作成したFormRequestを引数に渡してあげる
```php:
    public function store(ContactInputPost $request)
    {
        Contact::create($request->all());
        return redirect()->route('contacts.index')->with('success','登録完了');
    }
```

### 重複チェックをしてみた Ver 2
- 前回も重複チェックは行ったが、編集のときに自身の重複チェックが不自然になっていた
- [laravel属性の一意の検証ルールでモデルを更新](https://www.it-swarm.dev/ja/php/laravel%E5%B1%9E%E6%80%A7%E3%81%AE%E4%B8%80%E6%84%8F%E3%81%AE%E6%A4%9C%E8%A8%BC%E3%83%AB%E3%83%BC%E3%83%AB%E3%81%A7%E3%83%A2%E3%83%87%E3%83%AB%E3%82%92%E6%9B%B4%E6%96%B0/1045012394/)を参考に下記の手順で実装してみた
  - `edit.blade.php`のformタグ内に`<input type="hidden" name="id" value="{{ $contact->id }}">`を追記
  - FormRequestクラス (`ContactInputPost`) にRuleクラス (`use Illuminate\Validation\Rule;`) を追記
  - 現在のIDを無視する一意のルールを追加 `'mail' => ['required', Rule::unique('contacts')->ignore($this->id)],`
  - storeメソッドと同じようにupdateメソッドでフォームリクエストを引数に渡す

### 独自のバリデーションを追加してみた
- [独自のバリデーションルールの作成方法](https://tac-blog.tech/index.php/2018/09/08/add-validation-rule/)を参考に、「ひらがな」のみしか許可しないようなバリデーションを制作
- クロージャーを使用するパターンで試してみる
```php:
// ContactInputPost.php
public function rules()
{
    return [
        'name' => [
            'required',
            // クロージャーを使用したパターン
            function ($attr, $value, $fail) {
                // dump($attr, $value, $fail); // name, 値, Closure($message)
                if (preg_match('/[^ぁ-んー]/u', $value) !== 0)
                {
                    return $fail('ひらがなで入力してください');
                }
            }
        ],
```
- Ruleオブジェクトを使用するパターンでも試してみる
- `php artisan make:rule kana` で独自のルールオブジェクトを作成する
```php:
    // app/Rules/Kana.php
    // 検証ルール（マッチすればTrueを返す）
    public function passes($attribute, $value)
    {
        return preg_match('/[^ぁ-んー]/u', $value) === 0;
    }
    // エラーメッセージの実装
    public function message()
    {
        return 'ひらがなで入力してください';
    }
    
    // app/Http/Requests/ContactInputPost.php
    // 使用する場合はNewする
    public function rules()
    {
        return [
            'name' => [
                'required',
                new Kana()
            ],
```
- ここまでで紹介したやり方は`|`記法が使えない
  - `'tel' => 'required|max:15|not_regex:/[^0-9]/',` ←のようなことができない
  - `'name' => 'required|new Kana()` みたいなことはできない 
- 「ひらがな」のみみたいな検証ルールはどこでも使用できるほうが何度も書かなくてすみそう
- また、`|`記法を使用できるようにもしたい
- なので、**サービスプロバイダーに登録**してみる
- `php artisan make:provider KanaServiceProvider` でサービスプロバイダを作成する
```php:
use Illuminate\Support\Facades\Validator;
class KanaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extend('kana', function ($attr, $value, $parameters, $validator) {
            return preg_match('/[^ぁ-んー]/u', $value) === 0;
        });
    }
}
```
- 上記の用意ができたら、`config/app.php`の`providers`にクラスを登録して完了
- 使用する場合は、以下のような感じでOK
```php:
    public function rules()
    {
        return [
            // 配列でもかける
            // 'name' => [
            //     'required','kana'
            // ],
            'name' => 'required|kana',
        ];
    }
    public function messages(){
        return [
            'kana' => 'かなはひらがなで入力してください',
            'name.required' => '名前は必須です',
```

### File Upload機能を実装してみる
- 以前、Contactテーブルに「file_path」というカラムを用意したが、１つの問合せに対して複数のファイルをアップするときの保存先に困るので、テーブル設計を変更する
- ということで、`php artisan make:migration update_contacts_table --table=contacts` で再度マイグレーションファイルを作成を試みたが、エラーになる
- マイグレーションファイルは、クラスであるため、重複してしまうとエラーになってしまうのだ
  - [何度もマイグレーションファイル作るときに命名決める](https://qiita.com/naoqoo2/items/91ca0a8db2401059e56c)
  - `php artisan make:migration modify_contacts_20160128 --table=contacts`
  - ひとまず、クラス名の重複はこれで解決するので、下記のようにカラムを削除するマイグレーションを実装してから `php artisan migrate`
```php:
class ModifyContacts20160128 extends Migration
{
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            // ファイルパスは別テーブルに変更
            $table->dropColumn('file_path');
        });
    }

    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('file_path');
        });
    }
}
```
- 続いて、ファイル管理用のテーブルを作成する
- `php artisan make:model -m Attachment` を実行して、以下のようなテーブル定義で試してみる
```php:
    Schema::create('attachments', function (Blueprint $table) {
        $table->id();
        $table->integer('parent_id'); // 各テーブルのID (ContactテーブルのIDとか)
        $table->string('model')->comment('model-name'); // 各モデル名 (App\Contact)
        $table->string('path')->comment('file-path');  // ファイルのパス
        $table->string('key')->comment('key'); // 何のファイルかをグループ化するキー（profile_photosなど）
        $table->timestamps();
    });
```

# 参考サイト
- [MarkDown記法](https://notepm.jp/help/how-to-markdown)
- [VSCODEショートカット](https://qiita.com/naru0504/items/99495c4482cd158ddca8)
- [Laravel命名規則](https://qiita.com/gone0021/items/e248c8b0ed3a9e6dbdee)
- [Laravelベストプラクティス](https://webty.jp/staffblog/production/post-1835/)

# VSCODE拡張
- PHP Intelephense: PHPのコード補完、参照の検索や定義への移動などなど
- Dot ENV: .envファイルの色分けしてくれる
- [Laravel関係の拡張リスト](https://qiita.com/hitotch/items/9b5c8e28f50e0e3f7806)

# 便利
- blade上でデバッグ作業
```blade.php:
@php
    dd();
@endphp
```

### (復習) ServiceContainerとServiceProvider
- [ServiceContainerとServiceProviderの関係性](https://www.geekfeed.co.jp/geekblog/laravel-service-providers)
- ブートストラップ：初期化。Laravelのコア機能。依存関係やServiceContainerのルート登録とかやっている.リクエスト来るたびに`config/app.php`の`providers`に登録されたものを初期化して必要なアイテムを用意しておくイメージ。
- サービスコンテナ：アプリのブートストラップ処理で開始された全てのものが配置される Key=>Value の「場所」。「Auto resolving / Dependency injectionなど」などの強力な機能を搭載。ものをサービスコンテナに**バインド(配置)**して、サービスコンテナから**解決(取得)**している。
```php:
    // 配置
    app()->bind('example', function(){  
        return 'hello world';
    });
    // 解決
    app()->make('example'); // [出力] hello world
```
- サービスプロバイダー：サービスコンテナにものをバインドするために使用される。主要なメソッド「register/boot」がある。
- Laravelにリクエストが来ると、まずブートストラップが開始、登録済みサービスプロバイダの全てのregisterメソッドを呼び出し、次にbootメソッドを呼び出す。
- つまり、サービスプロバイダーの register メソッドを使用してサービスコンテナーに何かをバインドしていた場合、システムのブートストラップ後にすべてを使用できる。
-  プロジェクト内のどこからでもコンテナからこれらのものを使用できる
- [ServiceProviderのbootとregisterメソッド](https://blog.fagai.net/2015/04/12/laravel-serviceprovider-boot-register/)
  - 大抵は、bootメソッドに記述するだけで事足りる。他のServiceProviderでregisterしておかないといけない時とかに、registerは使うようだ。
- Deferredサービスプロバイダ：実は、サービスプロバイダはリクエストのタイミングで全てが呼び出されるわけでは無いらしい。Deferredとは、サービスプロバイダーがすべてのリクエストに対してロードされるのではなく、特にそのプロバイダーがリクエストされた場合にのみロードされることを意味する。
  - **サービスプロバイダーは、コンテナにバインディングを登録している場合にのみ「Deferred」にすることができる**
  - **bootメソッドに何かがある場合、そのサービスプロバイダーはDeferredにすることができない**
- Deferredサービスプロバイダーは DeferrableProvider インターフェイスと provides メソッドを使用する。
- Deferredの簡易的な例
```php:
namespace App\Providers;
 
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use App\Sample;
class SampleServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register() { 
        $this->app->bind(Sample::class, function($app){
            return new Sample();
        });
    }
    public function provides()
    {
        return [Sample::class];
    }
}
```

### 画像ってDBに保存するべきでない？
- MysqlではBLOB型にすれば、画像データを保存することができる
- そのまま保存することは良いことなのだろうか？
- 実を言うと、[あまりおすすめはされないようである](https://teratail.com/questions/81233)
  - 1レコードのデータ量が多くなり、クエリに時間がかかるようだ
    - 例えば、4kbの画像データでも、DBにとっては結構な負担のようだ
  - また、単純にストレージ容量が圧迫してしまう
    - そもそも、画像データ自体は、オブジェクトストレージ（S3とか）を活用していったほうがよい
  - WebとDBを分割する際に、ファイルパスなどで弊害がでてしまうようだ
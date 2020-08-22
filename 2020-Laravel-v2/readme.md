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


# 参考サイト
- [MarkDown記法](https://notepm.jp/help/how-to-markdown)
- [VSCODEショートカット](https://qiita.com/naru0504/items/99495c4482cd158ddca8)
- [Laravel命名規則](https://qiita.com/gone0021/items/e248c8b0ed3a9e6dbdee)
- [Laravelベストプラクティス](https://webty.jp/staffblog/production/post-1835/)

# VSCODE拡張
- PHP Intelephense: PHPのコード補完、参照の検索や定義への移動などなど
- Dot ENV: .envファイルの色分けしてくれる


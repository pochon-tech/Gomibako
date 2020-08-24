<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // NULLを許可する
        Schema::table('contacts', function (Blueprint $table) {
            $table->text('file_path')->nullable()->change();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // NULLを許可しないようにする
        Schema::table('contacts', function (Blueprint $table) {

            // 既にテーブルの対象カラムにNULLを登録しているならば必要
            // DB::statement('UPDATE `contacts` SET `file_path` = "" WHERE `file_path` IS NULL');
            // Laravel 5.5 以降の記述
            $table->text('file_path')->nullable(false)->change();
            // Laravel 5.4 以前の記述
            // DB::statement('ALTER contacts `book` MODIFY `file_path` TEXT NOT NULL;');
        });
        
    }
}

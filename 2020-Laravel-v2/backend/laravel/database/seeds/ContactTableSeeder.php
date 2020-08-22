<?php

use Illuminate\Database\Seeder;

class ContactTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // contactテーブルにデータをinsert
        DB::table('contacts')->insert([
            [
                'name' => '田中太郎',
                'tel' => '01100001111',
                'mail' => 'test@exammple.com',
                'contents' => 'お問い合わせをします',
                'file_path' => '',
            ]
        ]);
    }
}

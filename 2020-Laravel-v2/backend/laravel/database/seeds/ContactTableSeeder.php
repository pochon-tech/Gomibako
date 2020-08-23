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
            ],
            [
                'name' => '田中次郎',
                'tel' => '01100001111',
                'mail' => 'test2@exammple.com',
                'contents' => 'お問い合わせをします２',
                'file_path' => '',
            ],
            [
                'name' => '田中三郎',
                'tel' => '01100001111',
                'mail' => 'test3@exammple.com',
                'contents' => 'お問い合わせをします３',
                'file_path' => '',
            ],
            [
                'name' => '田中四郎',
                'tel' => '01100001111',
                'mail' => 'test4@exammple.com',
                'contents' => 'お問い合わせをします４',
                'file_path' => '',
            ],
        ]);
    }
}

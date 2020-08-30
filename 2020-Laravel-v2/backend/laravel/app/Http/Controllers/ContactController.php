<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Attachment;
use Illuminate\Http\Request;
use App\Http\Requests\ContactInputPost; 

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contacts = Contact::with('attachments')->latest('id')->paginate(3);
        return view('contacts.index', compact('contacts'))->with('i', (request()->input('page', 1) - 1) * 3);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('contacts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\ContactInputPost  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContactInputPost $request)
    {
        // お問い合わせテーブルの保存
        $res = Contact::create($request->all());

        // 画像データの保存
        if ($res && $request->hasFile('photos')) {
            foreach($request->photos as $photo) {
                // storage/app/public/attachments フォルダに保存
                $path = $photo->store('public/attachments');
                // crateは配列でいける https://laracasts.com/discuss/channels/eloquent/usercreate-return
                Attachment::create([
                    'parent_id' => $res->id,
                    'model' => get_class($res),
                    'path' => 'storage/attachments/'.basename($path),
                    'key' => 'photos'
                ]);
            }
        }

        return redirect()->route('contacts.index')->with('success','登録完了');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contact  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        return view('contacts.show',compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Contact  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        return view('contacts.edit',compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\ContactInputPost  $request
     * @param  \App\Contact  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ContactInputPost $request, Contact $contact)
    {
        // お問い合わせテーブルの更新;
        $res = $contact->update($request->all());
        // 画像データの保存
        if ($res && $request->hasFile('photos')) { 
            // 更新がある場合は、既存の画像を削除する
            foreach($contact->attachments as $attachment) $attachment->delete();
            foreach($request->photos as $photo) {
                $path = $photo->store('public/attachments');
                Attachment::create([
                    'parent_id' => $contact->id,
                    'model' => get_class($contact),
                    'path' => 'storage/attachments/'.basename($path),
                    'key' => 'photos'
                ]);
            }
        }

        return redirect()->route('contacts.index')->with('success','編集完了');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success','削除完了しました');
    }

    /**
     * 
     */
    public function download(Request $request)
    {

        // \DB::enableQueryLog();
        // foreach (Contact::cursor() as $contact) {
        //     var_dump($contact->id);
        // }
        // dd(\DB::getQueryLog());
        return response()->streamDownload(function(){
            $stream = fopen('php://output', 'w'); // 出力バッファOpen
            stream_filter_prepend($stream,'convert.iconv.utf-8/cp932//TRANSLIT'); // 文字コードをShift-JISに変換
            // CSVのヘッダを用意
            fputcsv($stream, [
                'id',
                'name',
                'tel',
                'mail',
                'contents'
            ]);
            // CSVのボディ（データ）を用意
            foreach (Contact::cursor() as $contact) {
                fputcsv($stream, [
                    $contact->id,
                    $contact->name,
                    $contact->tel,
                    $contact->mail,
                    $contact->contents
                ]);
            }
            fclose($stream); // 出力バッファClose
        }, 'contacts.csv', [ 'Content-Type' => 'application/octet-stream' ]);
    }
}

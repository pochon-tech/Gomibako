<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreUserPost;
use App\Services\UserService;

class UserController extends Controller
{
    /**
     * User一覧ページ
     *
     * @return View
     */
    public function index(): View
    {
        $users = (new UserService())->list();
        return view('user.list', ['users' => $users]);
    }
    /**
     * User登録ページ
     * 
     * @return View
     */
    public function create(): View
    {
        // postした内容を取得 Confirmページを設ける場合もこれを使う
        // Validation時に入力情報を残したいため、行っている
        return view('user.create', ['user' => \Session::get('_old_input')]);
    }
    /**
     * User登録処理
     * 
     * @return StoreUserPost $request
     * @return RedirectResponse 
     */
    public function store(StoreUserPost $request): RedirectResponse
    {
        $user = $request->all();
        if ((new UserService())->store($user)) {
            // with(): フラッシュデータ指定のリダイレクト
            // trans(): 多言語対応。resource/lang/ja/以下で定義する配列のキーを指定
            return redirect('/user/')->with('success_message', trans('message.success_save'));
        }
        return redirect('/user/')->with('error_message', trans('message.failed_save'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Contracts\Support\Renderable;

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
}

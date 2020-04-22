<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function escape()
    {
        return view('view.escape', [
            'msg' => '<img src="https://wings.msn.to/image/wings.jpg" title="logo" /><p>Hello</p>'
        ]);
    }
    public function if()
    {
        return view('view.if', [
            'random' => random_int(0,100)
        ]);
    }
    public function isset()
    {
        return view('view.isset', [
            'msg' => 'Hello',
            'msg2' => null
        ]);
    }

    public function switch()
    {
        return view('view.switch',  [
            'random' => random_int(1,5)
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;

class HelloController extends Controller
{
    public function index()
    {
        return 'Hello World!';
    }

    public function view()
    {
        $data = [
            'msg' => 'Hello World'
        ];
        return view('hello.view', $data);
    }

    public function list()
    {
        $data = [
            'records' => Book::all()
        ];
        return view('hello.list', $data);
    }
}

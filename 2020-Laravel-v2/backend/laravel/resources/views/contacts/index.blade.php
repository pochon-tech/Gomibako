@extends('contacts.layout')

{{-- @yield('title')にテンプレートごとにtitleタグの値を代入 --}}
@section('title', 'お問い合わせ一覧')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>お問い合わせ一覧</h2>
            </div>
        </div>
    </div>
    <table class="table table-bordered">
        <tr>
            <th>Name</th>
            <th>Mail</th>
            <th>Tel</th>
            <th>Contents</th>
        </tr>
        @foreach ($contacts as $contact)
            <tr>
                <td>{{ $contact->name }}</td>
                <td>{{ $contact->mail }}</td>
                <td>{{ $contact->tel }}</td>
                <td>{{ $contact->contents }}</td>
            </tr>
        @endforeach
    </table>
@endsection
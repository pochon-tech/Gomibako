@extends('contacts.layout')
  
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>お問い合わせの編集</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('contacts.index') }}">戻る</a>
        </div>
    </div>
</div>
   
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>エラー</strong> 入力内容に誤りがあります<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
   
<form action="{{ route('contacts.update',$contact->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" value="{{ $contact->id }}">
     <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                <input type="text" name="name" value="{{ old('name', $contact->name) }}" class="form-control" placeholder="Name">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Mail:</strong>
                <input type="text" name="mail" value="{{ old('mail', $contact->mail) }}" class="form-control" placeholder="Mail">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Tel:</strong>
                <input type="text" name="tel" value="{{ old('tel', $contact->tel) }}" class="form-control" placeholder="Tel">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Contents:</strong>
                <input type="text" name="contents" value="{{ old('contents', $contact->contents) }}" class="form-control" placeholder="Contents">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>File: </strong>        
                <input type="file" name="photos[]" class="form-control" multiple="multiple">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">編集</button>
        </div>
    </div>
   
</form>
@endsection
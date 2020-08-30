@extends('contacts.layout')
 
@section('title', 'お問い合わせ一覧')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>お問い合わせ一覧</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('contacts.create') }}"> 新規作成</a>
                <a class="btn btn-success" href="{{ route('contacts.download') }}"> CSVDL</a>
            </div>
        </div>
    </div>
   
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
   
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Mail</th>
            <th>Tel</th>
            <th>Contents</th>
            <th>file</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($contacts as $contact)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $contact->name }}</td>
            <td>{{ $contact->mail }}</td>
            <td>{{ $contact->tel }}</td>
            <td>{{ $contact->contents }}</td>
            <td>
                @foreach ($contact->attachments as $attachment)
                    <img src="{{ asset($attachment->path) }}" width="150px" />
                @endforeach
            </td>
            <td>
                <form action="{{ route('contacts.destroy',$contact->id) }}" method="POST">
   
                    <a class="btn btn-info" href="{{ route('contacts.show',$contact->id) }}">詳細</a>
    
                    <a class="btn btn-primary" href="{{ route('contacts.edit',$contact->id) }}">編集</a>
   
                    @csrf
                    @method('DELETE')
      
                    <button type="submit" class="btn btn-danger" onclick="return window.confirm('削除してもよいですか？');">
                        <span>削除</span>
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
  
    {!! $contacts->links() !!}
      
@endsection
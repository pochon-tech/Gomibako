<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hello World</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  </head>
  <body>
    <h1>isset</h1>
    @isset($msg)
      <p>変数$msgは「{{ $msg }}」である</p>
    @endisset
    @isset($msg2)
      <p>変数$msg2は「{{ $msg2 }}」である</p>
    @else
      <p>変数$msg2は「{{ $msg2 }}」である</p>
    @endisset
    @isset($msg3)
      <p>変数$msgは「{{ $msg3 }}」である</p>
    @else
      <p>そもそも変数$msg3はない</p>
    @endisset

    <h1>empty</h1>
    @empty($msg)
      <p>変数$msgは「{{ $msg }}」である</p>
    @else
      <p>変数$msgは「{{ $msg }}」である</p>
    @endisset
    @empty($msg2)
      <p>変数$msgは「{{ $msg2 }}」である</p>
    @endisset
    @empty($msg3)
      <p>そもそも変数$msg3はない</p>
    @endisset
    
  </body>
</html>
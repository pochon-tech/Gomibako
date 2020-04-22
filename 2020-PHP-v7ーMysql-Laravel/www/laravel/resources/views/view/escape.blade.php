<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hello World</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  </head>
  <body>
    {!! $msg !!}
    {{ $msg }}
    <p>@{{ $msg }}</p>
    @verbatim
    <p>{{ $msg }}</p>
    @endverbatim
    {{--comment--}}
  </body>
</html>
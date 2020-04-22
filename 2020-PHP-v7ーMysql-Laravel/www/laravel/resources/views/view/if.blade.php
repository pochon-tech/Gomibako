<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hello World</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  </head>
  <body>
    @if($random < 50)
      <p>{{ $random }}は50未満である</p>
    @elseif($random < 70)
      <p>{{ $random }}は50以上70未満である</p>
    @ else
      <p>{{ $random }}は70以上である</p>
    @endif

    @unless($random === 50)
     <p>{{ $random }}は50以外である</p>
    @endunless
  </body>
</html>
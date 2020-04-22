<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hello World</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  </head>
  <body>
    @switch($random)
      @case(5)
        <p>5</p>
        @break
      @case(4)
        <p>4</p>
        @break
      @case(3)
        <p>3</p>
        @break
      @case(2)
        <p>2</p>
        @break
      @default
        <p>1</p>
    @endswitch 
  </body>
</html>
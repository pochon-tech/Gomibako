<!DOCTYPE html>
  <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
      <head>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <title>Hello World</title>
      </head>
      <body>
            {{ date('Y年m月d日 h:i:s') }}
            {{ $msg }}
      </body>
  </html>
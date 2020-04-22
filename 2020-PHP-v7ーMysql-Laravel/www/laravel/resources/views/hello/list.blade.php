<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hello World</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  </head>
  <body>
    <table class="table">
      <tr>
        <th>書名</th>
        <th>価格</th>
        <th>出版社</th>
        <th>登録日</th>
     </tr>
      @foreach ($records as $record)
        <tr>
          <td>{{ $record->title }}</td>
          <td>{{ $record->price }}</td>
          <td>{{ $record->publisher }}</td>
          <td>{{ $record->published }}</td>
        </tr>
      @endforeach
    </table>
  </body>
</html>
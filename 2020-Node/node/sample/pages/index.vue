<html>
<head></head>
<body>
  <div>Hello !!</div>
  <div>{{$t("Page")}}</div>
  <div>{{ $t("Page2") }}</div>
  <div>{{  $t("Page3")  }}</div>
  <div>{{ $t('日本語') }}</div>
  <div>{{ '出力されてはいけない' }}</div>
  <div>{{ $text }}</div>
  <input type="text" name="sample" placeholder="{{$t('入力してください')}}" />
  <input type="text" name="sample" :placeholder="$t('入力してください２')" />
</body>
</html>
  

<html>
<head></head>
<body>
  <div>Hello !!</div>
  <div>{{$t("components1")}}</div>
  <div>{{ $t("components2") }}</div>
  <div>{{  $t("components3")  }}</div>
  <div>{{ $t('日本語') }}</div>
  <div>{{ '出力されてはいけない' }}</div>
  <div>{{ $text }}</div>
  <input type="text" name="sample" placeholder="{{$t('入力してください')}}" />
  <input type="text" name="sample" :placeholder="$t('入力してください２')" />
</body>
</html>
  

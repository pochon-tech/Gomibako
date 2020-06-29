<html>
<head></head>
<body>
  <div>Hello !!</div>
  <div>{{$t("Vue1")}}</div>
  <div>{{ $t("Vue2") }}</div>
  <div>{{  $t("Vue3")  }}</div>
  <div>{{ $t('日本語') }}</div>
  <div>{{ '出力されてはいけない' }}</div>
  <div>{{ $text }}</div>
  <input type="text" name="sample" placeholder="{{$t('入力してください')}}" />
  <input type="text" name="sample" :placeholder="$t('入力してください２')" />
</body>
</html>
  

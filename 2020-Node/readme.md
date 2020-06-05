

### TypeScriptでCSV出力

**準備**
```sh:
cd 2020-Node; docker-compose exec node sh
mkdir sample; cd sample;
mkdir html; vi html/index.html;
<html>
<head></head>
<body>
  <div>Hello !!</div>
  <div>{{ $t("Export") }}</div>
</body>
</html>
yarn init
yarn add ttypescript @types/node ts-node -D
yarn add csv
tsc --init
vi convert.ts
```

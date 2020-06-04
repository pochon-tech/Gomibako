<?php

echo "Hi";
echo "<pre>";

// Order BY
$arr = [
    ['id' => 2, 'name' => 'B'],
    ['id' => 1, 'name' => 'A'],
    ['id' => 3, 'name' => 'C']
];

function orderBy($items, $attr, $order)
{
    $sortedItems = [];
    foreach($items as $item) {
        // 変数がオブジェクトかどうか確認する
        // オブジェクトならば、アロー演算子でattrで指定された値を、オブジェクトでなければ、連想配列のキーを指定して取得
        $key = is_object($item) ? $item->$attr : $item[$attr];
        $sortedItems[$key] = $item;
    }
    if ($order === 'desc'){
        // krsort: 配列をキーで逆順にソート
        krsort($sortedItems);
    } else {
        // krsort: 配列をキーで昇順にソート
        ksort($sortedItems);
    }
    // array_values: 配列から全ての値を取り出し、数値添字をつけた配列を返す
    return array_values($sortedItems);
}

var_dump($arr);
var_dump(orderBy(
    $arr,
    'id',
    'desc'
));


//Example with StdClass
$json = '{ "foo": "bar", "number": 42 }';
$stdInstance = json_decode($json);
echo $stdInstance->foo . PHP_EOL; //"bar"
echo $stdInstance->number . PHP_EOL; //42
//Example with associative array
$array = json_decode($json, true); // 第２引数をtrueにすると返されるオブジェクトは連想配列形式
echo $array['foo'] . PHP_EOL; //"bar"
echo $array['number'] . PHP_EOL; //42
// オブジェクト配列形式のJSONもデコード可能
$json2 = '
{
    "res":
    {
        "blogData":
        [
            {
                "id":"0001",
                "title":"サンプル01",
                "day":
                [
                    {
                        "year":"2015",
                        "month":"03",
                        "_day":"31"
                    }
                ],
                "author":"fantmsite",
                "tag":
                [
                    "ブログ"
                ],
                "report":"ブログ内容サンプル01"
            },
            {
                "id":"0002",
                "title":"サンプル02",
                "day":
                [
                    {
                        "year":"2015",
                        "month":"04",
                        "_day":"01"
                    }
                ],
                "author":"fantmsite",
                "tag":
                [
                    "ブログ"
                ],
                "report":"ブログ内容サンプル02"
            }
        ]
    }
}';
var_dump(json_decode($json2));
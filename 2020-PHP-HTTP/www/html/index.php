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



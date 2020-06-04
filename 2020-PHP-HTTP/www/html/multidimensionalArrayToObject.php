<?php
$array = [
    'id'   => 1,
    'name' => 'sample',
    'list' => [
        'id'   => 10,
        'name' => 'test',
    ],
];
function array_to_object($array) {
  $obj = new stdClass;
  foreach($array as $k => $v) {
     if(strlen($k)) {
        if(is_array($v)) {
           $obj->{$k} = array_to_object($v);
        } else {
           $obj->{$k} = $v;
        }
     }
  }
  return $obj;
}
echo "<pre>";
var_dump(array_to_object($array));

$obj = array_to_object($array);

var_dump($obj->id);
var_dump($obj->list->id);
var_dump($array["id"]);
var_dump($array["list"]["id"]);
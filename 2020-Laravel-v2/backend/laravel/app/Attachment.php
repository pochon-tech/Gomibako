<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    // 定義しないと、create() を呼んだときに、 Add [parent_id] to fillable property to allow mass assignment on [App\Attachment]. みたいなエラーが出る。
    protected $fillable = ['parent_id', 'model', 'path', 'key'];
}

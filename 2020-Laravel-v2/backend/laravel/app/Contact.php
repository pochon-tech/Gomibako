<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\ContactDeleted; 

class Contact extends Model
{
    protected $fillable = ['name', 'mail', 'tel', 'contents'];

    protected $dispatchesEvents = [
        'deleted' => ContactDeleted::class
    ];

    // リレーションシップ
    public function attachments() {

        // attachmentsテーブルは他のモデルともリレーションを持つことを想定して、modelカラム用意している
        // 今回は、「attachments.parent_id」＝「contacts.id」、「attachments.model」＝「App\Contact」
        return $this->hasMany("App\Attachment", "parent_id", "id")
            ->where('model', self::class);  // 「App\Contact」のものだけ取得
    }
}

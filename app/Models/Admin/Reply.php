<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

//    $table->id('reply_id');
//              $table->longText('replies')->nullable();
//              $table->unsignedBigInteger('message_id');

 protected $primaryKey = 'reply_id';

    protected $fillable = [
        'replies',
        'message_id',
    ];

     public function replyMessageRelation()
    {
        return $this->belongsTo(Message::class, 'message_id', 'message_id');
    }
}

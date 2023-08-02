<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Message extends Model
{
    use HasFactory, HasApiTokens;

    protected $primaryKey = 'message_id';
    protected $fillable = [
        'message_title',
        'message_desc',
        'branch_id',
        'respond',
        'user_id',
        'role_id'
    ];

    public function messageReplyRelation()
    {
        return $this->hasMany(Reply::class, 'message_id', 'message_id');
    }
}

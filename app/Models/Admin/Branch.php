<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $primaryKey = 'branch_id';
    protected $fillable = [
        'branch_name',
        'location_id',
        'branch_desc',
        'user_id'
    ];
    
}

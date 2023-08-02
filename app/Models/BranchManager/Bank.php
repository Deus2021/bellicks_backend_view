<?php

namespace App\Models\BranchManager;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Bank extends Model
{
    use HasFactory, HasApiTokens;
    protected $fillable = [
        'bank_name',
        'account_no',
        'amount',
        // 'bank_id',
        'branch_id'
    ];
}

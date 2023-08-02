<?php

namespace App\Models\Cashier;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class Expense extends Model
{
    use HasFactory;
    protected $primaryKey='expenses_id';
    protected $fillable=[
     'amount',
     'description',
     'branch_id',
     'user_id'
    ];

    public function UserExpensesRelation ()
    {
        return $this->hasMany(User::class,'user_id','user_id');
    }

}

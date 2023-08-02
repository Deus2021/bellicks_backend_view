<?php

namespace App\Models\Cashier;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    use HasFactory;
    protected $primaryKey='penalty_id';
    protected $fillable=[
     'amount',
     'desc',
     'branch_id',
     'user_id',
     'customer_id'
    ];

    public function UserExpensesRelation ()
    {
        return $this->hasMany(User::class,'user_id','user_id');
    }

}

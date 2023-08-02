<?php

namespace App\Models\Cashier;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitingCustomer extends Model
{
    use HasFactory;

    protected $primaryKey='visiting_customer_id';
    // protected $table = "visiting_customer";
    protected $fillable=[
     'amount',
     'desc',
     'branch_id',
     'user_id',
     'customer_id'
    ];
}

<?php

namespace App\Models\Cashier;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistCustomer extends Model
{
    use HasFactory;

    protected $primaryKey='visiting_customer_id';
    protected $fillable=[
     'customer_amount',
     'customer_description',
     'branch_id',
     'user_id',
     'customer_id'
    ];
}

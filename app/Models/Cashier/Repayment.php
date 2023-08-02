<?php

namespace App\Models\Cashier;

use App\Models\Loan\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class Repayment extends Model
{
    use HasFactory;
    protected $table = 'repayment';
    protected $primaryKey = 'repayment_id';
    protected $fillable = [
        'loan_id',
        'repayment_amount',
        'branch_id',
        'user_id',
        'exceeds_amount',
        'less_amount',
        'expected_amount',
        'customer_id',
        'created_at',
    ];


    public function repayCustomerRelation()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }


    public function customersLoansRelation()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}

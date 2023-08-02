<?php

namespace App\Models\Loan;

use App\Models\Admin\Branch;
use App\Models\Admin\LoanType;
use App\Models\Cashier\Repayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $primaryKey = 'loan_id';
    protected $fillable = [
        'loan_type_id',
        'form_cost',
        'loan_amount',
        'rate_amount',
        'insurance_amount',
        'customer_id',
        'loan_days',
        'start_date',
        'end_date',
        'branch_id'
    ];

    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }

    public function customersLoansRelation()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function branchLoansRelation()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }


    public function loansLoanTypesRelation()
    {
        return $this->hasOne(LoanType::class, 'loan_type_id', 'loan_type_id');
    }

     public function loanRepaymentRelation()
    {
        return $this->hasMany(Repayment::class, 'loan_id', 'loan_id');
    }
}
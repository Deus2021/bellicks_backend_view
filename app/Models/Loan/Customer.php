<?php

namespace App\Models\Loan;

use App\Models\Admin\Branch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'customer_id';
    protected $fillable = [
        'customer_img',
        'customer_img_id',
        'nida_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_relation',
        'customer_gender',
        'customer_dob',
        'customer_residence',
        'customer_guarantee',
        'guarantor_name',
        'guarantor_gender',
        'guarantor_phone',
        'guarantor_photo',
        'guarantor_nida',
        'branch_id',
        'status'
    ];

    public function customerLoanRelation()
    {

        return $this->hasMany(Loan::class, 'customer_id', 'customer_id');
    }
    public function customerBranchRelation()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }


    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }
}

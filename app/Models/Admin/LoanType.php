<?php

namespace App\Models\Admin;

use App\Models\Loan\Loan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    use HasFactory;
    protected $primaryKey = "loan_type_id";
    protected $fillable = [
        "loan_type",
        "desc",
        "insurance",
        "duration",
        "rate",
        "fixed_penalty",
        "penalty_percentage"
    ];
}

<?php

namespace App\Models\Loan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempCustomer extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_name',
        'nida_number',
        'form_cost'
    ];

    protected $table = 'temporary_customers';
}

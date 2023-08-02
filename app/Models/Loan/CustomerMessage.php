<?php

namespace App\Models\Loan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerMessage extends Model
{
    use HasFactory;
    protected $primaryKey = "customer_message_id";
    protected $fillable = [
        'message_title',
        'message_description',
        'customer_response',
        'ussd_id'
    ];
}

<?php

namespace App\Models\BranchManager;

use App\Models\Admin\Branch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
class Inventory extends Model
{
    use HasFactory, HasApiTokens;
    protected $primaryKey = 'inventory_id';
    protected $fillable = [
        'branch_id',
        'inventory_name',
        'inventory_number',
        'inventory_price',
        'inventory_desc',
        'serial_no',
        'inventory_status',
        'DOR',
        'user_id'
    ];

    public function branchRelation()
    {
        return $this->belongsTo(Branch::class,'branch_id','branch_id');
    }
}
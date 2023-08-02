<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capital extends Model
{
    use HasFactory;
    protected $table='capitals';
    protected $primaryKey = 'capital_id';
    protected $fillable = [
        'capital_amount',
        'branch_id'
    ];


    public function capitalBranchRelation()
    {
        return $this->hasOne(Branch::class,'branch_id','branch_id');
    }
}
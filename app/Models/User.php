<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Admin\Branch;
use App\Models\Admin\Role;
use App\Models\Cashier\Expense;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;



    protected $primaryKey = 'user_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'phone',
        'profile_image',
        'id_img',
        'id_number',
        'id_type',
        'DOB',
        'employement_date',
        'salary',
        'email',
        'password',
        'access_id',
        'role_id',
        'branch_id'
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function roleRelation()
    {

        return $this->hasOne(Role::class, 'role_id', 'role_id');
    }

   /**
    * @ return user branch
    *
    */

    public function userBranchRelation()
    {
         return $this->hasOne(Branch::class, 'branch_id', 'branch_id');
    }
}
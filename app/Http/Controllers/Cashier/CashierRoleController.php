<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Admin\Role;
use App\Models\User;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;

class CashierRoleController extends Controller
{
    use HttpResponses;
    use Permissions;
    public function index(Request $request)
    {

        if ($this->isCashier($request->user())) {
            $branch_id=$request->user()->branch_id;
            // $users_of_branch=User::where('branch_id',$branch_id)->with(['roleRelation'])->get();

            $users_of_branch = User::where('branch_id', $branch_id)
                ->whereHas('roleRelation', function ($query) {
                    $query->where('role', '!=', 'super-admin');
                })
                ->with(['roleRelation'])
                ->get();

            return $this->success($users_of_branch);
        }

        return $this->error('', 'Unauthorized Access', 401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        // if($this->isCashier($request->user()))
        // {
        //   $role = Role::where('id',$id)->first();
        //   return $this->success($id);
        // }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Models\Admin\Role;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use HttpResponses;
    use Permissions;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($this->isAdmin($request->user())) {
            $role = Role::where('role', '!=', 'super-admin')->get();
            return $this->success($role);
        }

        return $this->error('', 'Unauthorized Access', 401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {

        if ($this->isAdmin($request->user())) {
            $request->validated($request->all());
            $role = Role::create([
                'role' => $request->input('role')
            ]);
            $roles = Role::all();
            return $this->success($roles);
        } else {

            return $this->error('', 'Unauthorized Access', 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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

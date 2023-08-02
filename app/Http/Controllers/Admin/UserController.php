<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use HttpResponses;
    use Permissions;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (
            $this->isAdmin($request->user()) ||
            $this->isLoanOfficer($request->user())
            || $this->isBranchManager($request->user())
            || $this->isCashier($request->user())
        ) {
            $user = User::with(['userBranchRelation', 'roleRelation'])->get();
            return $this->success($user);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {

        if ($this->isAdmin($request->user())) {
            $request->validated($request->all());
            $profileStorage = '/Profile_Image/';
            $IdStorage = '/Id_Image/';
            $fileIdStorage = $request->file('id_img');
            $id_img = $IdStorage . date('Y-m-d') . $fileIdStorage->getClientOriginalName();
            $fileIdStorage->storeAs('public', $id_img);

            $profile_image = $request->file('profile_image');
            $profile_img = $profileStorage . date('Y-m-d') . $profile_image->getClientOriginalName();
            $profile_image->storeAs('public', $profile_img);

            $user = User::create([
                'full_name' => $request->input('full_name'),
                'phone' => $request->input('phone'),
                'profile_image' => $profile_img,
                'id_img' => $id_img,
                'id_number' => $request->input('id_number'),
                'id_type' => $request->input('id_type'),
                'DOB' => $request->input('DOB'),
                'employement_date' => $request->input('employement_date'),
                'salary' => $request->input('salary'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'access_id' => 1,
                'role_id' => $request->input('permission'),
                'branch_id' => $request->input('branch_id')
            ]);
        } else {
            return $this->error('', 'Unauthorized Access', 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function myAccount(Request $request)
    {
        if (
            $this->isAdmin($request->user()) ||
            $this->isLoanOfficer($request->user())
            || $this->isBranchManager($request->user())
            || $this->isCashier($request->user())
        ) {
            $user = User::where('user_id', $request->user()->user_id)
                ->with(['userBranchRelation', 'roleRelation'])->get();
            return $this->success($user);
        }
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

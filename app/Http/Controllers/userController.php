<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class userController extends Controller
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
            $user = User::where('user_id', $request->user()->user_id)->with(['userBranchRelation', 'roleRelation'])->get();
            return $this->success($user);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     if (
    //         $this->isAdmin($request->user()) ||
    //         $this->isLoanOfficer($request->user())
    //         || $this->isBranchManager($request->user())
    //         || $this->isCashier($request->user())
    //     ) {
    //         $user_id = $request->user()->user_id;

    //         $user = User::where('user_id', $user_id);
    //         $user->update([
    //             'user_id' => $user_id,
    //             'password' => Hash::make($request->input('password')),
    //         ]);
    //     }
    //     return $this->error('', 'Unauthorized Access', 401);
    // }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'password' => 'required|string|min:8|confirmed|regex:/[a-zA-Z0-9]+/',
        ]);

        $user = $request->user();
        if ($this->isAdmin($user) || $this->isLoanOfficer($user) || $this->isBranchManager($user) || $this->isCashier($user)) {
            $user->update([
                'password' => Hash::make($validatedData['password']),
            ]);
            // $user = User::with(['userBranchRelation', 'roleRelation'])
            // ->get('password');

            // return $this->success(['user' => $user]);
        } else {
            return $this->error(['success' => false], 'Unauthorized Access', 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}

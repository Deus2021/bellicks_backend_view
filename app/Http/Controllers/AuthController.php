<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 *The main difference between `$request->user()` and `Auth::user()` in Laravel is the way they retrieve the authenticated user instance.
 *$request->user() retrieves the authenticated user instance from the current HTTP request object. This means that it will only return a user instance if the user has been properly authenticated for the current request. If the user is not authenticated, $request->user() will return null.
 *On the other hand, Auth::user() retrieves the authenticated user instance from the session. This means that it will only return a user instance if the user was previously authenticated during the current session. If the user is not authenticated or if the session has expired, Auth::user() will return null.
 *In general, you should use $request->user() when you need to retrieve the authenticated user instance within the context of a specific HTTP request. For example, if you need to check whether the authenticated user has permission to perform a certain action on a resource.
 *You should use Auth::user() when you need to retrieve the authenticated user instance globally across your application, such as in a middleware or service provider.
 */
class AuthController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        // $role = $request->session()->get('role');

        // return $role.'asdf';

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // $request->validated($request->all());
        $credentials = ['email' => $request->email, 'password' => $request->password];
        //check if authentication has been made

        if (!Auth::guard('web')->attempt($credentials, false, false)) {
            return $this->error('', 'Credentials do not match', 401);
        }
        $user = User::where('email', $request->email)->with(['roleRelation'])->first();

        $token = $user->createToken('Api Token of ' . $user->full_name, [$user->roleRelation->role])->plainTextToken;
        // $request->session()->put('role', $user->roleRelation->role);
        return $this->success(['role' => $user->roleRelation->role, "token" => $token]);
    }

    /**
     * Store a newly created resource in user table.
     */
    public function store(Request $request)
    {
        // $request->validated($request->all());

    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $request->validated($request->all());
        $user_id = $request->user_id;
        $pass = $request->password;
        $user = User::where('user_id', $user_id);
        $user->update(['password' => Hash::make($pass)]);
        return $this->success('Update successful');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
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

    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();
        return $this->success('true');
    }
}

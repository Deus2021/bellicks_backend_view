<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LastActivityController extends Controller
{
    use HttpResponses;
    use Permissions;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $threshold = Carbon::now()->subSeconds(15);

        $activeCustomers = User::where('last_activity_at', '>=', $threshold)
            ->with(['roleRelation', 'userBranchRelation'])
            ->get();

  
        return $this->success($activeCustomers);
    }

    /**'
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
<?php

namespace App\Http\Controllers\BranchManager;

use App\Http\Controllers\Controller;
use App\Models\Admin\Capital;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;

class CapitalController extends Controller
{
    use HttpResponses;
    use Permissions;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $capitals = Capital::with(['capitalBranchRelation'])->get();
        return $this->success($capitals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $branch_id = $request->branch;
        $capital_amount = $request->capital_amount;
        $capital = Capital::create([
            'capital_amount' => $capital_amount,
            'branch_id' => $branch_id
        ]);

        $capitals = Capital::with(['capitalBranchRelation'])->get();
        return $this->success($capitals);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //        fetch capital resource due to branch_id
        $branch_id = $request->user()->branch_id;

        $capitals = Capital::where("branch_id", '=', $branch_id)->with(['capitalBranchRelation'])->get();
        return $this->success($capitals);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $capital = Capital::where('capital_id', $request->capital_id);

        $capital->update(['capital_amount' => $capital->get('capital_amount')[0]->capital_amount + $request->edited_amount]);

        $capitals = Capital::with(['capitalBranchRelation'])->get();
        return $this->success($capitals);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

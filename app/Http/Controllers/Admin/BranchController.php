<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBranchRequest;
use App\Models\Admin\Branch;
use App\Models\Loan\Loan;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Unique;

class BranchController extends Controller
{
    use Permissions;
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($this->isAdmin($request->user())) {

            $branches = Branch::all();
            return $this->success($branches);
        }
    }


    public function store(StoreBranchRequest $request)
    {
        if ($this->isAdmin($request->user())) {
            $request->validated($request->all());
            $branch_name = $request->input('branch_name');
            $branch_desc = $request->input('branch_desc');

            $created_branch = Branch::create([
                'branch_name' => $branch_name,
                'location_id' => 1,
                'branch_desc' => $branch_desc,
                'user_id' => $request->user()->user_id
            ]);
            return response()->json($created_branch);
        }

        return $this->error('', 'Unauthorized Access', 401);
    }

    /**
     * Display the specified resource.
     */
    public function loansByBranch(Request $request)
    {

        $loans = Loan::with('branchLoansRelation')
            ->select('branch_id', DB::raw('SUM(loan_amount) as total_amount'))
            ->where('loan_status', 'approved')
            ->groupBy('branch_id')
            ->get();

        return $this->success($loans);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
    }
}

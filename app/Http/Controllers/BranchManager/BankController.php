<?php

namespace App\Http\Controllers\BranchManager;

use App\Http\Controllers\Controller;
use App\Http\Requests\BranchManager\StoreToBankRequest;
use App\Models\BranchManager\Bank;
use Illuminate\Http\Request;
use App\Traits\Permissions;
use App\Traits\HttpResponses;


class BankController extends Controller
{
    use HttpResponses;
    use Permissions;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($this->isBranchManager($request->user())||$this->isAdmin($request->user()) ||$this->isCashier($request->user())) {

            $banktransfer = Bank::all();
            return $this->success($banktransfer);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreToBankRequest $request)
    {

        if($this->isBranchManager($request->user())||$this->isAdmin($request->user()) ||$this->isCashier($request->user())){
            $branch_id = $request->user()->branch_id;
            $banktransfer = Bank::create([
              'branch_id' => $branch_id,
            //   'bank_id' => $request->input('bank_id'),
              'bank_name' => $request->input('bank_name'),
              'account_no' => $request->input('account_no'),
              'amount' => $request->input('amount'),

            ]);
            return response()->json(['banktransfer' => $banktransfer, 'message' => 'Amount successfully transferred']);
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

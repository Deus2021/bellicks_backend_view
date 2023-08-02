<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Cashier\Penalty;
use App\Models\Loan\Customer;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;

class PenaltiesController extends Controller
{
    use HttpResponses;
    use Permissions;
    public function index(Request $request)
    {
        if ($this->isCashier($request->user())) {
            $branch_id = $request->user()->branch_id;

            $customer = Customer::where('branch_id', $branch_id)
            ->where('payment_status', 'unpaid')
            ->where('loan_status', 'approved')
            ->with(['customerLoanRelation'])->get();
            return $this->success(['customer' => $customer], 'Customer Retrieved');
        }

        return $this->error('', 'Unauthorized Access', 401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validated($request->all());
        if ($this->isCashier($request->user())) {

            $amount = $request->input('amount');
            $description = $request->input('description');
            $branch_id=$request->user()->branch_id;
            $penaltie = Penalty::create([
                'amount' => $amount,
                'desc' => $description,
                'branch_id' =>$branch_id
            ]);
            return $this->success(['expenses',$penaltie]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
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
}

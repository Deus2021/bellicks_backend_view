<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Cashier\Penalty;
use App\Models\Cashier\VisitingCustomer;
use App\Models\Loan\Customer;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;

class VistNewCustomersController extends Controller
{
    use HttpResponses;
    use Permissions;
    public function index(Request $request)
    {
        if ($this->isCashier($request->user())) {
            $branch_id = $request->user()->branch_id;

            $customer = Customer::where('branch_id', $branch_id)
            ->where('payment_status', 'unpaid')
            ->where('loan_status', 'waiting_b')
            ->where('loan_status', 'approved')
            ->where('issue_status', 'issued')
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
        // $request->validated($request->all());
        if ($this->isCashier($request->user())) {

            $amount = $request->input('amount');
            $customer_id = $request->input('customer_id');
            $desc = $request->input('desc');
            $branch_id=$request->user()->branch_id;
            $vist = VisitingCustomer::create([
                'customer_id' => $customer_id,
                'amount' => $amount,
                'desc' => $desc,
                'user_id' => $request->input('rank'),
                'branch_id' =>$branch_id
            ]);
            return $this->success($vist);
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

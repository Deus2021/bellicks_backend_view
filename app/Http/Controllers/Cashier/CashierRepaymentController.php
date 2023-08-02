<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Cashier\Repayment;
use App\Models\Loan\Customer;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CashierRepaymentController extends Controller
{
    use Permissions;
    use HttpResponses;
    public function index(Request $request)
    {
        $today = Carbon::today();
        $branch_id = $request->user()->branch_id;
       if($this->isCashier($request->user())){

        $repayment = Repayment::with(['repayCustomerRelation'])
        ->where('branch_id',$branch_id)
        ->whereDate('created_at',$today)
        ->get();

        return $this->success($repayment);
     }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $customer_id)
    {
        if($this->isCashier($request->user()))
        {
            $customer = Repayment::with(['repayCustomerRelation'])
            ->where('customer_id', $customer_id)->get();

            return $this->success($customer);
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

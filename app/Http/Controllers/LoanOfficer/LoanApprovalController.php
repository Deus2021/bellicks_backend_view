<?php

namespace App\Http\Controllers\LoanOfficer;

use App\Http\Controllers\Controller;
use App\Models\Loan\Loan;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;

class LoanApprovalController extends Controller
{
    use Permissions;
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $loan = Loan::find($request->customer_id);
        //  return $this->success($loan->get());
        if ($loan->count() > 0) {
            $loan->loan_status = "declined"; // "pending", "approved", "rejected
            $loan->save();
            $loans = Loan::where("customer_id", $request->customer_id)->with(['customersLoansRelation'])->get();
            return $this->success($loans);
        } else {
            return $this->error('', 'Loan not found', 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($customer_id)
    {

        // $loan = Loan::find($customer_id);
        $loan = Loan::where("customer_id", $customer_id)->with(['customersLoansRelation']);
        // return $this->success($loan->get());
        if ($loan->count() > 0) {
            $loan->loan_status = "approved"; // "pending", "approved", "rejected
            $loan->save();
            return $this->success($loan->get());
        } else {
            return $this->error('', 'Loan not found', 404);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Loan $loan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan)
    {
        //
    }
}

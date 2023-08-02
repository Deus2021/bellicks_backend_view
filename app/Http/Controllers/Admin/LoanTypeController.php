<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLoanTypeRequest;
use App\Models\Admin\LoanType;
use App\Models\Loan\TempCustomer;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;

class LoanTypeController extends Controller
{
    use Permissions;
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($this->isLoanOfficer($request->user()) || $this->isCashier($request->user()) || $this->isAdmin($request->user())) {
            $customers = TempCustomer::all();
            $loan_type = LoanType::all();
            return $this->success(['loan_type'=>$loan_type, 'customers'=>$customers]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($this->isLoanOfficer($request->user()) || $this->isCashier($request->user()) || $this->isAdmin($request->user())) {
            // $request->validated($request->all());
            $loan_type = LoanType::create([
                "loan_type" => $request->loantype_name,
                "desc" => $request->loantype_desc,
                "insurance" => $request->insurance_percent,
                "duration" => $request->duration_days,
                "rate" => $request->interest_rate,
                "fixed_penalty" => $request->repayment_penalty_fixed,
                "penalty_percentage" => $request->repayment_penalty_fixed_percent
            ]);
            // $loan_types = LoanType::all();
            return $this->success($loan_type);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $loan_type_id)
    {
        if ($this->isLoanOfficer($request->user()) || $this->isCashier($request->user())) {

            $loan_type = LoanType::where('loan_type_id', $loan_type_id)->first();
            return $this->success($loan_type);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LoanType $loanType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoanType $loanType)
    {
        //
    }
}
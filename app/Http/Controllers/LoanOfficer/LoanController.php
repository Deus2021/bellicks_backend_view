<?php

namespace App\Http\Controllers\LoanOfficer;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanOfficer\StoreLoanRequest;
use App\Models\Cashier\Repayment;
use App\Models\Loan\Customer;
use App\Models\Loan\Loan;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    use Permissions;
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($this->isLoanOfficer($request->user()) || $this->isCashier($request->user())) {
            $loan = Loan::all();
            $pending_loans = Loan::where('loan_status', 'pending')
                ->with(['customersLoansRelation'])
                ->get();
            return $this->success(['loan' => $loan, 'pending_loans' => $pending_loans], 'Loan Retrieved');
        }

        return $this->error('', 'Unauthorized Access', 401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLoanRequest $request)
    {
        $request->validated($request->all());
        $customer_id = $request->input('customer_id');
        $customer = Customer::where('customer_id', $customer_id);

        $loan_debit = Loan::where('customer_id', $customer_id)->where('loan_status', 'pending')->count();


        if ($this->isLoanOfficer($request->user()) || $this->isCashier($request->user())) {

            if ($customer->get('status')[0]->status === 'blocked') {
                return $this->error('', 'This customer has been blacklisted
                ,Loan cannot be granted to this customer', 404);
            } else if ($loan_debit > 0) {
                return $this->error('', 'This customer have existing loan,Loan cannot be granted to this customer', 404);
            } else if ($customer->get('status')[0]->status === 'blocked' && $loan_debit > 0) {
                return $this->error('', 'This customer has been blacklisted
 and have existing loan,Loan cannot be granted to this customer', 404);
            } else {

                $loan = Loan::create([
                    'loan_amount' => $request->input('loan_amount'),
                    'form_cost' => $request->input('form_cost'),
                    'loan_type_id' => $request->input('loan_type_id'),
                    'rate_amount' => $request->input('rate_amount'),
                    'insurance_amount' => $request->input('insurance_amount'),
                    'loan_days' => 39,
                    'start_date' => Carbon::tomorrow(),
                    'end_date' => $this->addDaysWithoutSundays(38),
                    'customer_id' => $customer_id,
                    'branch_id' => $request->user()->branch_id,
                ]);
                return $this->success(['loan' => $loan], 'Loan Created With Loan Privilege');
            }
        }
        return $this->error('', 'Unauthorized Access', 401);
    }


    /**
     * @Add end date without sunday
     */

    function addDaysWithoutSundays($days)
    {
        $end_date = Carbon::tomorrow();

        for ($i = 0; $i < $days; $i++) {
            $end_date->addDay();
            if ($end_date->isSunday()) {
                $end_date->addDay();
            }
        }

        return $end_date;
    }



    /**
     * Display the specified resource.
     */
    public function show(Request $request, $customer_id)
    {
        if ($this->isLoanOfficer($request->user()) || $this->isCashier($request->user())) {

            $loan = Loan::where("customer_id", $customer_id)->with(['customersLoansRelation'])->get();

            return $this->success($loan);
        }
        return $this->error('', 'Unauthorized Access', 401);
    }


    public function LoanDeclined(Request $request)
    {

        if ($this->isLoanOfficer($request->user())) {
            $loan = Loan::where("loan_id", $request->loan_id);

            if ($loan->count() > 0) {

                $loan->update(['loan_status' => 'declined']);

                $customer_loans = Loan::where("customer_id", $request->customer_id)->with(['customersLoansRelation'])->get();

                return $this->success($customer_loans);
            } else {
                return $this->error('', 'Loan not found', 404);
            }
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
     * Display the specified resource.
     */

    public function LoanApproved(Request $request)
    {

        if ($this->isLoanOfficer($request->user())) {

            $loan = Loan::where("loan_id", $request->loan_id);

            if ($loan->count() > 0) {

                $loan->update(['loan_status' => 'waiting_b']);
                $customer_loans = Loan::where("customer_id", $request->customer_id)->with(['customersLoansRelation'])->get();
                return $this->success($customer_loans);
            } else {
                return $this->error('', 'Loan not found', 404);
            }
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

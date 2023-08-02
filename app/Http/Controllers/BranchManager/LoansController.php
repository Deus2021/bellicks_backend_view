<?php

namespace App\Http\Controllers\BranchManager;

use App\Http\Controllers\Controller;
use App\Models\Cashier\Repayment;
use App\Models\Loan\Customer;
use App\Models\Loan\Loan;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoansController extends Controller
{
    use Permissions;
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $loans = Customer::all();
        return $this->success($loans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $loan = Loan::find($request->id);

        if ($loan->count() > 0) {
            $loan->loan_status = "declined"; // "pending", "approved", "rejected
            $loan->save();
            $loans = Loan::with(['customersLoansRelation'])->get();
            return $this->success($loans);
        } else {
            return $this->error('', 'Loan not found', 404);
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {


    public function show(Request $request, $customer_id)
    {
        if ($this->isBranchManager($request->user())) {
            $loan = Loan::where("customer_id", $customer_id)->with(['customersLoansRelation'])->get();
            return $this->success($loan);
        }
        return $this->error('', 'Unauthorized Access', 401);
    }

    public function WaitApproval(Request $request)
    {
        if ($this->isBranchManager($request->user())) {
            $loans = Loan::where("loan_status", "waiting_b")
                ->with(['customersLoansRelation'])
                ->get();
            return $this->success($loans);
        }
        return $this->error('', 'Unauthorized Access', 401);
    }

    public function LoanDeclined(Request $request)
    {
        if ($this->isBranchManager($request->user())) {
            $loan = Loan::where("loan_id", $request->loan_id);

            if ($loan->count() > 0) {

                $loan->update(['loan_status' => 'declined']);

                $customer_loans = Loan::where("customer_id", $request->customer_id)->with(['customersLoansRelation'])->get();

                return $this->success($customer_loans);
            } else {
                return $this->error('', 'Loan not found', 404);
            }
        }
        return $this->error('', 'Unauthorized Access', 401);
    }

    /**
     * Display the specified resource.
     */

    public function LoanApproved(Request $request)
    {

        if ($this->isBranchManager($request->user())) {

            $loan = Loan::where("loan_id", $request->loan_id);

            if ($loan->count() > 0) {

                $loan->update(['loan_status' => 'approved']);

                $customer_loans = Loan::where("customer_id", $request->customer_id)->with(['customersLoansRelation'])->get();
                $this->repaymentDays(0, $request, $request->loan_id, $loan, $request->customer_id);

                return $this->success($customer_loans);
             
            } else {
                return $this->error('', 'Loan not found', 404);
            }
        }
    }

    public function repaymentDays($repayment_amount, $request, $loan_id, $loan, $customer_id)
    {
        // $startDate = Carbon::now()->startOfDay();
        // $numDays = 47;
        // $expected_amount = ($loan->get('loan_amount')[0]->loan_amount) * 0.03;
        // for ($i = 1; $i <$numDays; $i++) {
        //     $date = $startDate->copy()->addDays($i); 

        //     if ($date->dayOfWeek === Carbon::SUNDAY) {
        //         continue; // Skip Sundays
        //     }

        //     $repayment = new Repayment();
        //     $repayment->loan_id = $loan_id;
        //     $repayment->repayment_amount = $repayment_amount;
        //     $repayment->branch_id = $request->user()->branch_id;
        //     $repayment->user_id = $request->user()->user_id;
        //     $repayment->created_at = $date;
        //     $repayment->updated_at = $date;
        //     $repayment->customer_id = $customer_id;
        //     $repayment->expected_amount =
        //         $expected_amount;
        //     $repayment->exceeds_amount = 0;
        //     $repayment->less_amount = 0;
        //     $repayment->save();
        // }
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
        //
    }
}
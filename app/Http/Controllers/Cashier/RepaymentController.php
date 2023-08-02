<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Cashier\Repayment;
use App\Models\Loan\Customer;
use App\Models\Loan\Loan;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RepaymentController extends Controller
{

    use HttpResponses;
    use Permissions;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $repayment_amount = $request->input('repayment_amount');

        $unpaid_loans = Loan::where('customer_id', $customer_id)
            ->where('payment_status', 'unpaid')
            ->where('loan_status', 'approved');

        if ($unpaid_loans->count() > 0) {
            $pay = $unpaid_loans->get('debit_amount');

            if ($repayment_amount > $pay[0]->debit_amount) {
                return $this->success('', 'Repayment amount exceeds debit amount', 202);
            } else {
                $loan = $pay[0]->debit_amount - $repayment_amount;
                if ($loan == 0) {
                    $unpaid_loans->update(['payment_status' => 'paid', 'debit_amount' => 0]);

                    return $this->success('', 'No pending debit', 202);
                } else {

                    $unpaid_loans->update(['payment_status' => 'unpaid', 'debit_amount' => $loan]);
                    return $this->success('', 'Customer still has pending debit', 202);
                }
            }
        } else {
            $check_loan_status =  Loan::where('customer_id', $customer_id)
                ->where('loan_status', 'declined')
                ->orWhere('loan_status', 'pending')
                ->get('*');

            $check_loan_status_paid =  Loan::where('customer_id', $customer_id)
                ->where('loan_status', 'approved')
                ->orWhere('loan_status', 'paid')
                ->get('*');

            if ($check_loan_status_paid[0]->payment_status == 'paid' && $check_loan_status_paid[0]->loan_status == 'approved') {
                return $this->success('', 'Customer has no pending debit', 202);
            } else {
                return $this->success('', 'Your loan is ' . $check_loan_status[0]->loan_status, 202);
            }
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
     * Display the specified resource.
     */

    public function repay(Request $request)
    {
        //check if customer have unpaid days
        $customer_id = $request->input('customer_id');
        $repayment_amount = $request->input('repayment_amount');
        $unpaid_loans = Loan::where('customer_id', $customer_id)
            ->where('payment_status', 'unpaid')
            ->where('loan_status', 'approved');
        // $today = Carbon::today();

        $loan_id = $unpaid_loans->get('loan_id')[0]->loan_id;

        $repayement = Repayment::create([
            'loan_id' => $loan_id,
            'repayment_amount' => $repayment_amount,
            'branch_id'=>$request->user()->branch_id,
            'user_id'=>$request->user()->user_id,
            'customer_id'=> $customer_id,
            'created_at'=>Carbon::today()
        ]);


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

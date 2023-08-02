<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Cashier\Repayment;
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
        $today = Carbon::today();

        $loan_id = $unpaid_loans->get('loan_id')[0]->loan_id;
        $previous_unapaid_loans = Repayment::whereDate('created_at', '<=', $today)
            ->where('loan_id', $loan_id)
            ->where('repayment_amount', 0)
            ->orWhere('less_amount', '!=', 0)
            ->orderBy('created_at', 'DESC')
            ->get();

        if ($previous_unapaid_loans->count() > 0) {
            $expec = $previous_unapaid_loans[0]->expected_amount;
            if ($repayment_amount >= $expec) {
                $days_to_be_covered_by_repayment_amount = $repayment_amount /  $expec;
                $divisible_by_expec = $repayment_amount %  $expec;

                if ($divisible_by_expec == 0) {  //if divisible by $expec
                    $date_of_old_day = $previous_unapaid_loans[$previous_unapaid_loans->count() - 1]->created_at;
                    // return $previous_unapaid_loans;
                    $arr = array();
                    for ($i = 0; $i < $days_to_be_covered_by_repayment_amount; $i++) {
                        $start = Carbon::parse($date_of_old_day)->addDays($i);
                        $repay = Repayment::whereDate('created_at', '=', $start)
                            ->where('loan_id', $loan_id);

                        $amount_to_pay = $previous_unapaid_loans[$i]->repayment_amount + ($repayment_amount / $days_to_be_covered_by_repayment_amount);

                        if ($amount_to_pay >  $expec) {
                            $excessAmount = $amount_to_pay -  $expec;

                            $repay->update([
                                'repayment_amount' =>  $expec,
                                'expected_amount' => 0,
                                'exceeds_amount' => $excessAmount,
                                'less_amount' => 0,
                            ]);
                            $this->store($request);
                        } else if ($amount_to_pay ==  $expec) {

                            $repay->update([
                                'repayment_amount' => $amount_to_pay,
                                'expected_amount' => 0,
                                'exceeds_amount' => 0,
                                'less_amount' => 0,
                            ]);
                            $this->store($request);
                        } else {
                            $repay->update([
                                'repayment_amount' => $amount_to_pay,
                                'expected_amount' => 0,
                                'exceeds_amount' => 0,
                                'less_amount' =>  $expec - $amount_to_pay,
                            ]);
                            $this->store($request);
                        }
                    }
                    // return $arr;
                } else {

                    //not divisible by $expect
                    // return $previous_unapaid_loans;
                    $reminder = $repayment_amount %  $expec;

                    $payment_days = intval($repayment_amount /  $expec);
                    $date_of_old_day = $previous_unapaid_loans[$previous_unapaid_loans->count() - 1]->created_at;
                    if ($reminder == 0) {
                        for ($i = 0; $i < $payment_days; $i++) {
                            $start = Carbon::parse($date_of_old_day)->addDays($i);
                            $repay = Repayment::whereDate('created_at', '=', $start)
                                ->where('loan_id', $loan_id);

                            if ($repayment_amount > $expec) {
                                $repay->update([
                                    'repayment_amount' =>
                                    $repayment_amount / $payment_days,
                                    'expected_amount' => 0,
                                    'exceeds_amount' => $repayment_amount - $expec,
                                    'less_amount' => 0,
                                ]);
                                $this->store($request);
                            } else {
                                $repay->update([
                                    'repayment_amount' =>
                                    $repayment_amount,
                                    'expected_amount' => 0,
                                    'exceeds_amount' => 0,
                                    'less_amount' =>  $expec - $repayment_amount,
                                ]);
                                $this->store($request);
                            }
                        }
                    } else {
                        $last_iteration = 0;
                        $amount_to_pay_after_reminder = ($repayment_amount - $reminder) / $payment_days;
                        for ($i = 0; $i < $payment_days; $i++) {
                            $start = Carbon::parse($date_of_old_day)->addDays($i);
                            $repay = Repayment::whereDate('created_at', '=', $start)
                                ->where('loan_id', $loan_id);
                            $repay->update([
                                'repayment_amount' =>
                                $amount_to_pay_after_reminder,
                                'expected_amount' => 0,
                                'exceeds_amount' => 0,
                                'less_amount' => 0,
                            ]);
                            $this->store($request);
                            $last_iteration = $i;
                        }

                        $start = Carbon::parse($date_of_old_day)->addDays($last_iteration + 1);
                        $repay = Repayment::whereDate('created_at', '=', $start)
                            ->where('loan_id', $loan_id);
                        $repay->update([
                            'repayment_amount' =>  $reminder,
                            'expected_amount' =>  $expec - $reminder,
                            'exceeds_amount' => 0,
                            'less_amount' => $expec - $reminder,
                        ]);
                        $this->store($request);
                    }


                    // not divisible by $expect

                }
            } else {
                $date_of_old_day = $previous_unapaid_loans[$previous_unapaid_loans->count() - 1]->created_at;
                $start = Carbon::parse($date_of_old_day);
                $repay = Repayment::whereDate('created_at', '=', $start)
                    ->where('loan_id', $loan_id);
                $amount_to_pay = $previous_unapaid_loans[$previous_unapaid_loans->count() - 1]->repayment_amount + ($repayment_amount);

                // return $amount_to_pay;
                if ($amount_to_pay >  $expec) {

                    $excessAmount = $amount_to_pay -  $expec;

                    $repay->update([
                        'repayment_amount' =>  $amount_to_pay,
                        'expected_amount' => $expec - $repayment_amount,
                        'exceeds_amount' => $excessAmount,
                        'less_amount' => $expec - $repayment_amount,
                    ]);
                    $this->store($request);
                } else if ($amount_to_pay ===  $expec) {
                    $repay->update([
                        'repayment_amount' => $amount_to_pay,
                        'expected_amount' => 0,
                        'exceeds_amount' => 0,
                        'less_amount' => 0,
                    ]);
                    $this->store($request);
                } else {
                    $repay->update([
                        'repayment_amount' => $amount_to_pay,
                        'expected_amount' => $expec - $amount_to_pay,
                        'exceeds_amount' => 0,
                        'less_amount' =>  $expec - $amount_to_pay,
                    ]);
                    $this->store($request);
                }
            }
        } else {

            //if no previous unpaid loans
            //check if customer have paid loans
            $unpaid_loans = Loan::where('customer_id', $customer_id)
                ->where('payment_status', 'unpaid')
                ->where('loan_status', 'approved');

            $loan_id = $unpaid_loans->get('loan_id')[0]->loan_id;
            $day_to_update = Carbon::tomorrow();
            $repay_for_next = Repayment::whereDate('created_at', '=', $day_to_update)
                ->where('loan_id', $loan_id)
                ->where('repayment_amount', 0);

            $expec = $repay_for_next->get('expected_amount')[0]->expected_amount;

            if ($repayment_amount >= $expec) {
                $days_to_be_covered_by_repayment_amount = $repayment_amount / $expec;
                $divisible_by_expec = $repayment_amount % $expec;

                if ($divisible_by_expec == 0) {
                    $amount_to_pay = $repayment_amount / $days_to_be_covered_by_repayment_amount;

                    for ($i = 0; $i < $days_to_be_covered_by_repayment_amount; $i++) {
                        $day_to_update = Carbon::tomorrow()->addDays($i);
                        $repay_for_next = Repayment::whereDate('created_at', '=', $day_to_update)
                            ->where('loan_id', $loan_id)
                            ->where('repayment_amount', 0);

                        if ($amount_to_pay ==  $expec) {

                            $repay_for_next->update([
                                'repayment_amount' => $amount_to_pay,
                                'expected_amount' => 0,
                                'exceeds_amount' => 0,
                                'less_amount' => 0,
                            ]);
                            $this->store($request);
                        } else {
                            return 'less than  $expec';
                            $repay_for_next->update([
                                'repayment_amount' => $amount_to_pay,
                                'expected_amount' => 0,
                                'exceeds_amount' => 0,
                                'less_amount' =>  $expec - $amount_to_pay,
                            ]);
                            $this->store($request);
                            return 0;
                        }
                    }
                } else {
                    // not devisible by  $expec
                    $reminder = $repayment_amount %  $expec;
                    $payment_days = intval($repayment_amount /  $expec);
                    if ($reminder == 0) {

                        for ($i = 0; $i < $payment_days; $i++) {
                            $day_to_update = Carbon::tomorrow()->addDays($i);
                            $repay_for_next = Repayment::whereDate('created_at', '=', $day_to_update)
                                ->where('loan_id', $loan_id)
                                ->where('repayment_amount', 0);

                            if ($repayment_amount > $expec) {
                                $repay_for_next->update([
                                    'repayment_amount' =>
                                    $repayment_amount / $payment_days,
                                    'expected_amount' => 0,
                                    'exceeds_amount' => $repayment_amount - $expec,
                                    'less_amount' => 0,
                                ]);
                                $this->store($request);
                            } else {
                                $repay_for_next->update([
                                    'repayment_amount' =>
                                    $repayment_amount / $payment_days,
                                    'expected_amount' => 0,
                                    'exceeds_amount' => 0,
                                    'less_amount' =>  $expec - $repayment_amount,
                                ]);
                                $this->store($request);
                            }
                        }
                    } else {
                        $last_iteration = 0;
                        $amount_to_pay_after_reminder = ($repayment_amount - $reminder) / $payment_days;
                        for ($i = 0; $i < $payment_days; $i++) {
                            $day_to_update = Carbon::tomorrow()->addDays($i);
                            $repay_for_next = Repayment::whereDate('created_at', '=', $day_to_update)
                                ->where('loan_id', $loan_id)
                                ->where('repayment_amount', 0);
                            $repay_for_next->update([
                                'repayment_amount' =>
                                $amount_to_pay_after_reminder,
                                'expected_amount' => 0,
                                'exceeds_amount' => 0,
                                'less_amount' => 0,
                            ]);
                            $this->store($request);
                            $last_iteration = $i;
                        }

                        $day_to_update = Carbon::tomorrow()->addDays($last_iteration + 1);
                        $repay_for_next = Repayment::whereDate('created_at', '=', $day_to_update);
                        $repay_for_next->update([
                            'repayment_amount' =>  $reminder,
                            'expected_amount' => 0,
                            'exceeds_amount' => 0,
                            'less_amount' => $expec - $reminder,
                        ]);
                        $this->store($request);
                    }
                }
            } else {
                $repay_for_next = Repayment::whereDate('created_at', '>=', $today)
                    ->where('loan_id', $loan_id)
                    ->where('repayment_amount', 0);
                $repay_for_next->get()[0]->update([
                    'repayment_amount' => $repayment_amount,
                    'expected_amount' => 0,
                    'exceeds_amount' => 0,
                    'less_amount' =>  $expec - $repayment_amount,
                ]);
                $this->store($request);
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //


    }
}
<?php

namespace App\Http\Controllers\BranchManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cashier\Repayment;
use App\Models\Loan\Loan;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    use HttpResponses;
    use Permissions;

    public
        $filter = null;
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



        if (!is_null($request->date_data) && !is_null($request->loans_data)) {
            $date = Carbon::now()->subDays($request->date_data['value']);
            $payment_status = $request->loans_data['value'];
            /**
             * $payment_status =0 means paid
             * $payment_status =1 means underpaid
             * $payment_status =2 means notpaid at all
             */
            if ($payment_status == 0) {
                $branch_id = $request->user()->branch_id;
                $filter = Loan::with('customersLoansRelation')
                    ->where('branch_id', $branch_id)
                    ->whereDate('created_at', '>=', $date)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            } else if ($payment_status == 1) {
                $branch_id = $request->user()->branch_id;
                $filter = Repayment::with('customersLoansRelation')
                    ->where('branch_id', $branch_id)
                    ->whereDate('created_at', '>=', $date)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            } else if ($payment_status == 2) {
                $branch_id = $request->user()->branch_id;
                $filter = Repayment::with('customersLoansRelation')
                    ->where('exceeds_amount', '!=', 0)
                    ->where('branch_id', $branch_id)
                    ->whereDate('created_at', '>=', $date)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            }
        }
        /**
         * Get loans payment status by branch data
         */
        if (!is_null($request->loans_data)) {
            $payment_status = $request->loans_data['value'];
            if ($payment_status == 0) {
                $branch_id = $request->user()->branch_id;
                $filter = Loan::with('customersLoansRelation')
                    ->where('branch_id', $branch_id)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            } else if ($payment_status == 1) {
                $branch_id = $request->user()->branch_id;
                $filter = Repayment::with('customersLoansRelation')
                    ->where('less_amount', '!=', 0)
                    ->where('branch_id', $branch_id)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            } else if ($payment_status == 2) {
                $branch_id = $request->user()->branch_id;
                $filter = Repayment::with('customersLoansRelation')
                    ->where('exceeds_amount', '!=', 0)
                    ->where('branch_id', $branch_id)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            }
        }

        /**
         * Get loans payment status by date
         */
        if (!is_null($request->loans_data) && !is_null($request->date_data)) {
            $date = Carbon::now()->subDays($request->date_data['value']);
            $payment_status = $request->loans_data['value'];
            if ($payment_status == 0) {
                $filter = Loan::with('customersLoansRelation')
                    ->whereDate('created_at', '>=', $date)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            } else if ($payment_status == 1) {
                $filter = Repayment::with('customersLoansRelation')
                    ->where('less_amount', '!=', 0)
                    ->whereDate('created_at', '>=', $date)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            } else if ($payment_status == 2) {
                $filter = Repayment::with('customersLoansRelation')
                    ->where('exceeds_amount', '!=', 0)
                    ->whereDate('created_at', '>=', $date)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            }
        }
        /**
         * Get loans payment status by branch and date
         */
        if (!is_null($request->date_data)) {
            $date = Carbon::now()->subDays($request->date_data['value']);
            $branch = $request->branch_data['value'];
            $branch_id = $request->user()->branch_id;
            $filter = Repayment::with('customersLoansRelation')
                ->where('branch_id', $branch_id)
                ->whereDate('created_at', '>=', $date)
                ->get()
                ->unique('customersLoansRelation.customer_id');
            return $this->success($filter->values()->toArray());
        }

        if (!is_null($request->date_data) && isset($request->date_data['value'])) {
            $date = Carbon::now()->subDays($request->date_data['value']);
            $filter = Repayment::with('customersLoansRelation')
                ->whereDate('created_at', '>=', $date)
                ->get()
                ->unique('customersLoansRelation.customer_id');
            return $this->success($filter->values()->toArray());
        } else {
        }

        // $branch = null;
        // if (!is_null($request->branch_data) && isset($request->branch_data['value'])) {
        //     $branch = $request->branch_data['value'];
        //     $branch_id = $request->user()->branch_id;
        //     $filter = Loan::with('customersLoansRelation')
        //         ->where('branch_id', $branch_id)
        //         ->get()
        //         ->unique('customersLoansRelation.customer_id');
        //     return $this->success($filter->values()->toArray());
        // } else {
        // }

        $payment_status = null;
        if (!is_null($request->loans_data)) {
            $payment_status = $request->loans_data['value'];
            if ($payment_status == 0) {
                $loan_data = [];
                $loans = Loan::with('customersLoansRelation')
                    ->where('payment_status', 'paid')
                    ->get();
                foreach ($loans as $loan) {
                    $loan_id = $loan->loan_id;
                    $repayments = Repayment::where('loan_id', $loan_id)->get();

                    $loan_info = [
                        'customer_name' => $loan->customersLoansRelation->customer_name,
                        'customer_phone' => $loan->customersLoansRelation->customer_phone,
                        'nida_number' => $loan->customersLoansRelation->nida_number,
                        'guarantor_phone' => $loan->customersLoansRelation->guarantor_phone,
                        'loan_amount' => $loan->loan_amount + $loan->rate_amount,
                        'amount_paid' => $repayments->sum('repayment_amount'),
                        'outstanding_balance' => ($loan->loan_amount + $loan->rate_amount) - $repayments->sum('repayment_amount'),
                    ];

                    array_push($loan_data, $loan_info);
                }


                return $this->success($loan_data);
            } else if ($payment_status == 1) {
                $loans = Loan::with(['customersLoansRelation'])
                    ->where('payment_status', 'unpaid')
                    ->get();

                $under_paid = [];
                foreach ($loans as $loan) {
                    $repayments = Repayment::where('loan_id', $loan->loan_id)->get();

                    if ($repayments->sum('repayment_amount') < ($loan->loan_amount + $loan->rate_amount)&& $repayments->sum('repayment_amount')!=0) {
                        $loan_info = [
                            'customer_name' => $loan->customersLoansRelation->customer_name,
                            'customer_phone' => $loan->customersLoansRelation->customer_phone,
                            'nida_number' => $loan->customersLoansRelation->nida_number,
                            'guarantor_phone' => $loan->customersLoansRelation->guarantor_phone,
                            'loan_amount' => $loan->loan_amount + $loan->rate_amount,
                            'amount_paid' => $repayments->sum('repayment_amount'),
                            'outstanding_balance' => ($loan->loan_amount + $loan->rate_amount) - $repayments->sum('repayment_amount'),
                        ];

                        array_push($under_paid, $loan_info);
                    }
                }
                return $this->success($under_paid);
            } else if ($payment_status == 2) {

                $filter = Loan::with(['customersLoansRelation'])
                    ->where('payment_status', 'unpaid')
                    ->get();
                $not_paid_at_all = [];
                foreach ($filter as $fil) {
                    $check = Repayment::where('loan_id', $fil->loan_id);
                    if ($check->count() > 0) {
                    } else {
                        $loan_info = [
                            'customer_name' => $fil->customersLoansRelation->customer_name,
                            'customer_phone' => $fil->customersLoansRelation->customer_phone,
                            'nida_number' => $fil->customersLoansRelation->nida_number,
                            'guarantor_phone' => $fil->customersLoansRelation->guarantor_phone,
                            'loan_amount' => $fil->loan_amount + $fil->rate_amount,
                            'amount_paid' => $check->sum('repayment_amount'),
                            'outstanding_balance' => ($fil->loan_amount + $fil->rate_amount) - $check->sum('repayment_amount'),
                        ];
                        array_push($not_paid_at_all, $loan_info);
                    }
                }
                return $this->success($not_paid_at_all);
            } else if ($payment_status == 3) {
                $loans = Loan::with(['customersLoansRelation'])
                    ->where('payment_status', 'unpaid')
                    ->get();
                foreach ($loans as $loan) {
                    $loan_id = $loan->loan_id;
                    $loan_amount = $loan->loan_amount;
                    $start_date = $loan->start_date;
                    $end_loan_date = Carbon::parse($loan->end_date);
                    $expected_of_day = $loan_amount * 0.03;

                    $payments = Repayment::select('loan_id', DB::raw('SUM(repayment_amount) as total_repayment_amount'))
                        ->where('loan_id', $loan_id)
                        ->groupBy('loan_id')
                        ->get();

                    if ($payments->count() > 0) {
                        if ($end_loan_date >= Carbon::now()) {
                            foreach ($payments as $payment) {
                                $days_covered_by_paid_amount = $payment->total_repayment_amount / $expected_of_day;
                                if ($payment->total_repayment_amount % $expected_of_day == 0) {
                                    $date_covered_by_paid_amount = Carbon::parse($start_date)->addDays($days_covered_by_paid_amount);

                                    if ($date_covered_by_paid_amount >= Carbon::now()) {

                                    }

                                    if ($date_covered_by_paid_amount->isCurrentWeek()) {

                                    }

                                    if ($date_covered_by_paid_amount->isCurrentMonth()) {

                                    }

                                } else {

                                    $wholeNumber = intval($payment->total_repayment_amount % $expected_of_day);
                                }
                            }
                        } else {
                            // loan has expired
                        }
                    } else {
                        // loan has not been paid at all
                    }
                }
            }
        } else {
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



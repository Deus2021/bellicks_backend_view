<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cashier\Repayment;
use App\Models\Loan\Customer;
use App\Models\Loan\Loan;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    use HttpResponses;
    use Permissions;
    /**
     * Display a listing of the resource.
     */


    function getDaysInMonthWithoutSundays($year, $month)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $days = [];
        $currentDate = $startDate;

        while ($currentDate <= $endDate) {
            if ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
                $days[] = $currentDate->copy();
            }
            $currentDate->addDay();
        }

        return count($days);
    }


    public function numberOfDaysInYear()
    {
        $currentYear = Carbon::now()->year;

        $startOfYear = Carbon::createFromDate($currentYear, 1, 1);
        $endOfYear = Carbon::createFromDate($currentYear, 12, 31);

        $totalDays = $endOfYear->diffInDays($startOfYear) + 1;
        $numberOfSundays = $startOfYear->diffInWeeks($endOfYear->copy()->endOfWeek(Carbon::SUNDAY));

        $numberOfDaysWithoutSundays = $totalDays - $numberOfSundays;

        return $numberOfDaysWithoutSundays;
    }

    public function index()
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();
        $today = Carbon::today();
        $repayment_today = Repayment::whereDate('created_at', '=', $today)->get();

        $repayment_week = Repayment::whereDate('created_at', '>=', $startOfWeek)
            ->whereDate('created_at', '<=', $endOfWeek)
            ->get();

        // $numberOfDays = Carbon::now()->daysInMonth;


        $repayment_month = Repayment::whereDate('created_at', '>=', $startOfMonth)
            ->whereDate('created_at', '<=', $endOfMonth)
            ->get();

        $repayment_year = Repayment::whereDate('created_at', '>=', $startOfYear)
            ->whereDate('created_at', '<=', $endOfYear)
            ->get();

        $now = Carbon::now();
        $currentYear = $now->year;
        $currentMonth = $now->month;
        $daysInMonth = $this->getDaysInMonthWithoutSundays($currentYear, $currentMonth);
        $repayment_today = Repayment::whereDate('created_at', '=', $today)->get();

        $loans = Loan::where('loan_status', 'approved')
            ->where('payment_status', 'unpaid')
            ->get();
        $total_expected_of_day = 0;
        $total_expected_of_week = 0;
        $total_expected_of_month = 0;
        $total_expected_of_day_for_loan_not_paid_at_all = 0;
        $total_expected_of_week_for_loan_not_paid_at_all = 0;
        $total_expected_of_month_for_loan_not_paid_at_all = 0;

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
                                // excess
                                // remove from expected
                            } else if ($date_covered_by_paid_amount < Carbon::now()) {
                                // less
                                $total_expected_of_day += $expected_of_day;
                            }

                            if ($date_covered_by_paid_amount->isCurrentWeek()) {
                                // excess
                                $total_expected_of_week += $expected_of_day;
                            }

                            if ($date_covered_by_paid_amount->isCurrentMonth()) {
                                $total_expected_of_month += $expected_of_day;
                            }
                        } else {
                            $wholeNumber = intval($payment->total_repayment_amount % $expected_of_day);
                            $total_expected_of_day += $expected_of_day;
                            $total_expected_of_week += $expected_of_day;
                            $total_expected_of_month += $expected_of_day;
                        }
                    }
                } else {
                    // loan has expired
                }
            } else {
                if (Carbon::today() >= Carbon::parse($start_date)) {
                    $total_expected_of_day_for_loan_not_paid_at_all += $expected_of_day;
                    $total_expected_of_week_for_loan_not_paid_at_all += $expected_of_day * 6;
                    $total_expected_of_month_for_loan_not_paid_at_all += $expected_of_day * $daysInMonth;
                }
            }
        }

        $loans_year = Loan::whereDate('created_at', '>=', $startOfYear)
            ->whereDate('created_at', '<=', $endOfYear)
            ->where('loan_status', 'approved')
            ->where('payment_status', 'unpaid')
            ->get();

        // return $this->success([
        //     'repayment_today' => $total_expected_of_day_for_loan_not_paid_at_all + $total_expected_of_day,
        //     'repayment_week' => $total_expected_of_week * 6 + ($total_expected_of_week_for_loan_not_paid_at_all),
        //     'repayment_month' => $total_expected_of_month * $daysInMonth + ($total_expected_of_month_for_loan_not_paid_at_all),
        //     'repayment_year' => $loans_year->sum(function ($loan) {
        //         return $loan->loan_amount + $loan->rate_amount;
        //     }),
        //     'actual_of_day' => $repayment_today->sum('repayment_amount'),
        //     'total_repayment' =>
        //     $loans_year->sum(function ($loan) {
        //         return $loan->loan_amount + $loan->rate_amount;
        //     }),
        //     'loans' => $this->loans(),
        //     'form' => $this->income(),
        //     'insurance' => $this->insurance(),
        // ]);



        return $this->success([
            'repayment_today' => $total_expected_of_day_for_loan_not_paid_at_all + $total_expected_of_day,
            'repayment_week' => $repayment_week->sum('repayment_amount'),
            'repayment_month' => $repayment_month->sum('repayment_amount'),
            'repayment_year' =>  $repayment_year->sum('repayment_amount'),
            'actual_of_day' => $repayment_today->sum('repayment_amount'),
            'total_repayment'=> $repayment_year->sum('repayment_amount'),
            'loans' => $this->loans(),
            'form' => $this->income(),
            'insurance' => $this->insurance(),
        ]);
    }


    public function loans()
    {
        //
        $today = Carbon::today();
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();
        $loans_today = Loan::whereDate('created_at', '=', $today)
            ->where('loan_status', 'approved')
            ->where('payment_status', 'unpaid')
            ->get();

        $loans_week = Loan::whereDate('created_at', '>=', $startOfWeek)
            ->whereDate('created_at', '<=', $endOfWeek)
            ->where('loan_status', 'approved')
            ->where('payment_status', 'unpaid')
            ->get();




        $loans_month = Loan::whereDate('created_at', '>=', $startOfMonth)
            ->whereDate('created_at', '<=', $endOfMonth)
            ->where('loan_status', 'approved')
            ->where('payment_status', 'unpaid')
            ->get();



        $loans_year = Loan::whereDate('created_at', '>=', $startOfYear)
            ->whereDate('created_at', '<=', $endOfYear)
            ->where('loan_status', 'approved')
            ->where('payment_status', 'unpaid')
            ->get();

        $total_loans = Loan::where('loan_status', 'approved')
            ->where('payment_status', 'unpaid')
            ->get();
        $last_ten_loan = Loan::latest()
            ->where('loan_status', 'approved')
            ->where('payment_status', 'unpaid')
            ->with(['customersLoansRelation'])
            ->take(10)
            ->get();

        return [
            'loans_today' => $loans_today->sum(function ($loan) {
                return $loan->loan_amount + $loan->rate_amount;
            }),
            'loans_week' => $loans_week->sum(function ($loan) {
                return $loan->loan_amount + $loan->rate_amount;
            }),
            'loans_month' => $loans_month->sum(function ($loan) {
                return $loan->loan_amount + $loan->rate_amount;
            }),
            'loans_year' => $loans_year->sum(function ($loan) {
                return $loan->loan_amount + $loan->rate_amount;
            }),
            'total_loans' => $total_loans->sum(function ($loan) {
                return $loan->loan_amount + $loan->rate_amount;
            }),
            'last_ten_loan' => $last_ten_loan
        ];
    }

    public function income()
    {
        $form = Loan::all();
        return $form->sum('form_cost');
    }
    public function insurance()
    {

        $today = Carbon::today();
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();

        $insurance = Loan::all();

        $insurance_today = Loan::whereDate('created_at', '=', $today)
            ->get();
        $insurance_week = Loan::whereDate('created_at', '>=', $startOfWeek)
            ->whereDate('created_at', '<=', $endOfWeek)
            ->get();
        $insurance_month = Loan::whereDate('created_at', '>=',  $startOfMonth)
            ->whereDate('created_at', '<=', $endOfMonth)
            ->get();
        $insurance_year = Loan::whereDate('created_at', '>=', $startOfYear)
            ->whereDate('created_at', '<=', $endOfYear)
            ->get();
        return [
            'insurance_today' => $insurance_today->sum('insurance_amount'),
            'insurance_week' => $insurance_week->sum('insurance_amount'),
            'insurance_month' => $insurance_month->sum('insurance_amount'),
            'insurance_year' => $insurance_year->sum('insurance_amount'),
            'total_insurance' => $insurance->sum('insurance_amount'),
        ];
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

<?php

namespace App\Http\Controllers\LoanOfficer;

use App\Http\Controllers\Controller;
use App\Models\Cashier\Repayment;
use App\Models\Loan\Loan;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    use HttpResponses;

    public function index(Request $request)
    {
        $branch_id = $request->user()->branch_id;

        $today = Carbon::today();
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();

        $repayment_today = Repayment::where("branch_id", '=', $branch_id)
            ->whereDate('created_at', '=', $today)->get();

        $repayment_week = Repayment::where("branch_id", '=', $branch_id)
            ->whereDate('created_at', '>=', $startOfWeek)
            ->whereDate('created_at', '<=', $endOfWeek)
            ->get();


        $repayment_month = Repayment::where("branch_id", '=', $branch_id)
            ->whereDate('created_at', '>=', $startOfMonth)
            ->whereDate('created_at', '<=', $endOfMonth)
            ->get();


        $repayment_year = Repayment::where("branch_id", '=', $branch_id)
            ->whereDate('created_at', '>=', $startOfYear)
            ->whereDate('created_at', '<=', $endOfYear)
            ->get();

        $total_repayment = Repayment::where("branch_id", '=', $branch_id)->get();


        return $this->success([
            'repayment_today' => $repayment_today->sum('expected_amount'),
            'repayment_week' => $repayment_week->sum('expected_amount'),
            'repayment_month' => $repayment_month->sum('expected_amount'),
            'repayment_year' => $repayment_year->sum('expected_amount'),
            'actual_of_day' => $repayment_today->sum('repayment_amount'),
            'total_repayment' => $total_repayment->sum('repayment_amount'),
            'loans' => $this->loans($branch_id),
            'form' => $this->income($branch_id),
            'insurance' => $this->insurance($branch_id),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function loans($branch_id)
    {
        //
        $today = Carbon::today();
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();
        $loans_today = Loan::where("branch_id", $branch_id)->whereDate('created_at', $today)
            ->where('loan_status', 'approved')
            ->where('payment_status', 'unpaid')
            ->get();

        $loans_week = Loan::where("branch_id", $branch_id)->whereDate('created_at', '>=', $startOfWeek)
            ->whereDate('created_at', '<=', $endOfWeek)
            ->where('loan_status', 'approved')
            ->where('payment_status', 'unpaid')
            ->get();


        $loans_month = Loan::where("branch_id", $branch_id)->whereDate('created_at', '>=', $startOfMonth)
            ->whereDate('created_at', '<=', $endOfMonth)
            ->where('loan_status', 'approved')
            ->where('payment_status', 'unpaid')
            ->get();


        $loans_year = Loan::where("branch_id", $branch_id)->whereDate('created_at', '>=', $startOfYear)
            ->whereDate('created_at', '<=', $endOfYear)
            ->where('loan_status', 'approved')
            ->where('payment_status', 'unpaid')
            ->get();

        $total_loans = Loan::where("branch_id", $branch_id)->where('loan_status', 'approved')
            ->where('payment_status', 'unpaid')
            ->get();
        $last_ten_loan = Loan::where("branch_id", $branch_id)->latest()
            ->where('loan_status', 'approved')
            ->where('payment_status', 'unpaid')
            ->with(['customersLoansRelation'])
            ->take(10)
            ->get();


        return [
            'loans_today' => $loans_today->sum('debit_amount'),
            'loans_week' => $loans_week->sum('debit_amount'),
            'loans_month' => $loans_month->sum('debit_amount'),
            'loans_year' => $loans_year->sum('debit_amount'),
            'total_loans' => $total_loans->sum('debit_amount'),
            'last_ten_loan' => $last_ten_loan
        ];
    }

    public function income($branch_id)
    {
        $form = Loan::where("branch_id", $branch_id)->get();
        return $form->sum('form_cost');
    }
    public function insurance($branch_id)
    {

        $today = Carbon::today();
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();

        $insurance = Loan::where("branch_id", $branch_id)->get();

        $insurance_today = Loan::where("branch_id", $branch_id)->whereDate('created_at', '=', $today)
            ->get();
        $insurance_week = Loan::where("branch_id", $branch_id)->whereDate('created_at', '>=', $startOfWeek)
            ->whereDate('created_at', '<=', $endOfWeek)
            ->get();
        $insurance_month = Loan::where("branch_id", $branch_id)->whereDate('created_at', '>=',  $startOfMonth)
            ->whereDate('created_at', '<=', $endOfMonth)
            ->get();
        $insurance_year = Loan::where("branch_id", $branch_id)->whereDate('created_at', '>=', $startOfYear)
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
}

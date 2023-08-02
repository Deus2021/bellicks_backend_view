<?php

namespace App\Http\Controllers\BranchManager;

use App\Http\Controllers\Controller;
use App\Models\Loan\Customer;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    use HttpResponses;
    use Permissions;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $branch_id = $request->user()->branch_id;

        if ($this->isBranchManager($request->user())) {
            $customers = Customer::where('branch_id', $branch_id)
                ->with(['customerBranchRelation'])->get();
            return $this->success($customers);
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
    public function show(string $id)
    {
        $customers = Customer::with(['customerBranchRelation'])
            ->where('branch_id', $id)
            ->get();
        return $this->success($customers);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $customer = Customer::find($request->customer_id);
        $customer->update(['status' => $request->customer_status]);
        $customers = Customer::with(['customerBranchRelation'])->get();
        return $this->success($customers);
    }
    public function getCustomer(Request $request)
    {
        $today = Carbon::today();
        $branch_id = $request->user()->branch_id;

        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();

        $customers_today = Customer::where('branch_id', $branch_id)->whereDate('created_at', $today)->get();

        $customers_week = Customer::where('branch_id', $branch_id)->whereDate('created_at', '>=', $startOfWeek)
            ->whereDate('created_at', '<=', $endOfWeek)
            ->get();


        $customers_month = Customer::where('branch_id', $branch_id)->whereDate('created_at', '>=', $startOfMonth)
            ->whereDate('created_at', '<=', $endOfMonth)
            ->get();


        $customers_year = Customer::where('branch_id', $branch_id)->whereDate('created_at', '>=', $startOfYear)
            ->whereDate('created_at', '<=', $endOfYear)
            ->get();

        $customers = Customer::where('branch_id', $branch_id)->get();

        return $this->success([
            'customer_today' => $customers_today,
            'customer_week' => $customers_week,
            'customer_month' => $customers_month,
            'customer_year' => $customers_year,
            'total_customer' => $customers->count()
        ]);
        //        }
    }

    public function getCustomersData(Request $request): \Illuminate\Http\JsonResponse
    {
        $branch_id = $request->user()->branch_id;
        $customers = Customer::select(DB::raw("DATE_FORMAT(created_at, '%b') as month"), DB::raw("COUNT(*) as count"))
            ->where('branch_id', $branch_id)
            ->groupBy('month')
            ->orderBy('created_at', 'ASC')
            ->get();

        $categories = [];
        $customerData = [];

        foreach ($customers as $customer) {
            $categories[] = $customer->month;
            $customerData[] = $customer->count;
        }

        return response()->json([
            'categories' => $categories,
            'customerData' => $customerData,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function customerWithOutLoans(Request $request)
    {
        $customers = Customer::with(['customerBranchRelation', 'customerLoanRelation'])
            ->whereHas('customerBranchRelation', function ($query) use ($request) {
                $query->where('branch_id', $request->user()->branch_id);
            })
            ->whereHas('customerLoanRelation', function ($query) {
                $query->where('debit_amount', 0);
            }) // Optional: If you only want customers with loans
            ->get();

        return $this->success($customers);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function customerWithLoans(Request $request)
    {
        $customers = Customer::with(['customerBranchRelation', 'customerLoanRelation'])
            ->whereHas('customerBranchRelation', function ($query) use ($request) {
                $query->where('branch_id', $request->user()->branch_id);
            })
            ->whereHas('customerLoanRelation', function ($query) {
                $query->where('debit_amount', '!=', 0);
            }) // Optional: If you only want customers with loans
            ->get();

        return $this->success($customers);
    }

    public function customerDetails()
    {
        $today = Carbon::today();
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();

        $customers_today = Customer::whereDate('created_at', $today)->get();

        $customers_week = Customer::whereDate('created_at', '>=', $startOfWeek)
            ->whereDate('created_at', '<=', $endOfWeek)
            ->get();




        $customers_month = Customer::whereDate('created_at', '>=', $startOfMonth)
            ->whereDate('created_at', '<=', $endOfMonth)
            ->get();




        $customers_year = Customer::whereDate('created_at', '>=', $startOfYear)
            ->whereDate('created_at', '<=', $endOfYear)
            ->get();

        $customers = Customer::all();

        return $this->success([
            'customer_today' => $customers_today,
            'customer_week' => $customers_week,
            'customer_month' => $customers_month,
            'customer_year' => $customers_year,
            'total_customer' => $customers->count()
        ]);
    }
}

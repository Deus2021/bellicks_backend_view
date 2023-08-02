<?php

namespace App\Http\Controllers\Admin;

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
        if ($this->isAdmin($request->user())) {
            $customers = Customer::with(['customerBranchRelation'])->get();
            return $this->success($customers);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
   

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
    public function getCustomersData()
    {
        $customers = Customer::select(DB::raw("DATE_FORMAT(created_at, '%b') as month"), DB::raw("COUNT(*) as count"))
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
                $query->where('branch_id', $request->branches_id);
            })
            ->whereHas('customerLoanRelation', function ($query) {
            $query->where('payment_status', 'paid');
            })
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
                $query->where('branch_id', $request->branches_id);
            })
            ->whereHas('customerLoanRelation', function ($query) {
                $query->where('payment_status', 'unpaid');
            }) 
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
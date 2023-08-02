<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Cashier\Repayment;
use App\Models\Loan\Customer;
use App\Models\Loan\Loan;
use App\Models\Loan\TempCustomer;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CashierCustomerController extends Controller
{
    use HttpResponses;
    use Permissions;

    public function index(Request $request): JsonResponse
    {

        if ($this->isLoanOfficer($request->user()) || $this->isCashier($request->user())) {
            $branch_id = $request->user()->branch_id;

            $customer = Customer::where('branch_id', $branch_id)->with(['customerLoanRelation'])->get();
        }
        return $this->success(['customer' => $customer,'debtleft'=> $this->debtleft($request)]);
        // return $this->error('', 'Unauthorized Access', 401);
    }

    public function debtleft(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $debt_left = Loan::where('customer_id',$customer_id)
        ->where('payment_status', 'unpaid')
        ->where('issue_status', 'approved');
        $loan_amount_left=0;
        foreach ($debt_left as $loan)
        {
            $loan_id = $loan->loan_id;
            $rate_amount = $loan->rate_amount;
            $loan_amount = $loan->loan_amount;
            $total_loan = $loan_amount + $rate_amount;
            $total_paid = Repayment::where('loan_id',$loan_id);
            $total_paid = $total_paid->sum('repayment_amount');
            $loan_amount_left = $total_loan - $total_paid;
        }
        // echo $loan_amount_left;
        return $this->success($loan_amount_left);

    }
    /**
     * Store a newly created resource in storage.
     */
    public function reports(Request $request)
    {

        if (!is_null($request->date_data) && !is_null($request->status_data)) {
            $date = Carbon::now()->subDays($request->date_data['value']);
            $branch = $request->branch_data['value'];
            $status = $request->status_data['value'];

            if ($status == 0) {
                $filter = Loan::with('customersLoansRelation')
                    ->where('loan_status', 'approved')
                    ->where('branch_id', $branch)
                    ->whereDate('created_at', '>=', $date)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            }
             else if ($status == 1) {
                $filter = Loan::with('customersLoansRelation')
                    ->where('loan_status', 'pending')
                    ->where('branch_id', $branch)
                    ->whereDate('created_at', '>=', $date)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            } else if ($status == 2) {
                $filter = Loan::with('customersLoansRelation')
                    ->where('issue_status', 'issued')
                    ->where('branch_id', $branch)
                    ->whereDate('created_at', '>=', $date)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            }
            else if ($status == 3) {
                $filter = Loan::with('customersLoansRelation')
                    ->where('issue_status', 'Not issued')
                    ->where('branch_id', $branch)
                    ->whereDate('created_at', '>=', $date)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            }
        }

        if (!is_null($request->status_data) && !is_null($request->branch_data)) {
            $branch = $request->branch_data['value'];
            $status = $request->status_data['value'];
            if ($status == 0) {
                $filter = Loan::with('customersLoansRelation')
                    ->where('debit_amount', 0)
                    ->where('branch_id', $branch)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            } else if ($status == 1) {
                $filter = Repayment::with('customersLoansRelation')
                    ->where('less_amount', '!=', 0)
                    ->where('branch_id', $branch)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            } else if ($status == 2) {
                $filter = Repayment::with('customersLoansRelation')
                    ->where('exceeds_amount', '!=', 0)
                    ->where('branch_id', $branch)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            }
        }
        if (!is_null($request->status_data) && !is_null($request->date_data)) {
            $date = Carbon::now()->subDays($request->date_data['value']);
            $status = $request->status_data['value'];
            if ($status == 0) {
                $filter = Loan::with('customersLoansRelation')
                    ->where('loan_status', 'approved')
                    ->whereDate('created_at', '>=', $date)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            }
            // else if ($status == 10) {
            //     $filter = Customer::with(['customersLoansRelation'])->get();
            //     return $this->success($filter->values()->toArray());
            // }

            else if ($status == 1) {
                $filter = Loan::with('customersLoansRelation')
                    ->where('loan_status', 'pending')
                    ->whereDate('created_at', '>=', $date)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            } else if ($status == 2) {
                $filter = Loan::with('customersLoansRelation')
                    ->where('issue_status', 'issued')
                    ->whereDate('created_at', '>=', $date)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            }
            else if ($status == 3) {
                $filter = Loan::with('customersLoansRelation')
                    ->where('issue_status', 'Not issued')
                    ->whereDate('created_at', '>=', $date)
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            }
        }

        if (!is_null($request->branch_data) && !is_null($request->date_data)) {
            $date = Carbon::now()->subDays($request->date_data['value']);
            $branch = $request->branch_data['value'];
            $filter = Repayment::with('customersLoansRelation')
                ->where('branch_id', $branch)
                ->whereDate('created_at', '>=', $date)
                ->get()
                ->unique('customersLoansRelation.customer_id');
            return $this->success($filter->values()->toArray());
        }

        if (!is_null($request->date_data) && isset($request->date_data['value'])) {
            $date = Carbon::now()->subDays($request->date_data['value']);
            $filter = Customer::with('customersLoansRelation')
                ->whereDate('created_at', '>=', $date)
                ->get()
                ->unique('customersLoansRelation.customer_id');
            return $this->success($filter->values()->toArray());
        } else {
        }

        $branch = null;
        if (!is_null($request->branch_data) && isset($request->branch_data['value'])) {
            $branch = $request->branch_data['value'];
            $filter = Loan::with('customersLoansRelation')
                ->where('branch_id', $branch)
                ->get()
                ->unique('customersLoansRelation.customer_id');
            return $this->success($filter->values()->toArray());
        } else {
        }

        $status = null;
        if (!is_null($request->status_data)) {
            $status = $request->status_data['value'];
            if ($status == 0) {
                $filter = Loan::with(['customersLoansRelation' => function ($query) {
                    $query->select('customer_id', 'customer_name', 'customer_phone', 'nida_number', 'guarantor_phone');
                }])
                    ->where('loan_status', 'approved')
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                $filter->makeHidden(['loan_id', 'loan_type_id', 'customer_id', 'branch_id']);
                return $this->success($filter->values()->toArray());
            } else if ($status == 1) {
                $filter = Loan::with('customersLoansRelation')
                    ->where('loan_status', 'pending')
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                return $this->success($filter->values()->toArray());
            } else if ($status == 2) {
                $filter = Loan::with(['customersLoansRelation' => function ($query) {
                    $query->select('customer_id', 'customer_name', 'customer_phone', 'nida_number', 'guarantor_phone');
                }])
                    ->where('issue_status', 'issued')
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                $filter->makeHidden(['loan_id', 'loan_type_id', 'customer_id', 'branch_id']);
                return $this->success($filter->values()->toArray());
            }
            else if ($status == 3) {
                $filter = Loan::with(['customersLoansRelation' => function ($query) {
                    $query->select('customer_id', 'customer_name', 'customer_phone', 'nida_number', 'guarantor_phone');
                }])
                    ->where('issue_status', 'Not issued')
                    ->get()
                    ->unique('customersLoansRelation.customer_id');
                $filter->makeHidden(['loan_id', 'loan_type_id', 'customer_id', 'branch_id']);
                return $this->success($filter->values()->toArray());
            }
        } else {
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $customer_id)
    {
        if ($this->isLoanOfficer($request->user()) || $this->isCashier($request->user())) {

            $customer = Customer::where('customer_id', $customer_id)->with(['customerLoanRelation'])->first();

            return $this->success($customer);
        }
    }


    public function getCustomer(Request $request): \Illuminate\Http\JsonResponse
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

    public function getCustomersData(Request $request)
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

    public function customerWithOutLoans(Request $request): \Illuminate\Http\JsonResponse
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerWithLoans(Request $request): \Illuminate\Http\JsonResponse
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
    public function temporaryCustomers(Request $request)
    {
        $customer_name = $request->input('customer_name');
        $nida_number = $request->input('nida_number');
        $form_cost = $request->input('form_cost');
        $temp_customer = TempCustomer::where('nida_number', $nida_number)->first();
        if ($temp_customer->count() > 0) {
            return $this->error('', 'Customer Already Exist', 400);
        } else {
            TempCustomer::create([
                'customer_name' => $customer_name,
                'nida_number' => $nida_number,
                'form_cost' => $form_cost,
            ]);
        }
    }


}

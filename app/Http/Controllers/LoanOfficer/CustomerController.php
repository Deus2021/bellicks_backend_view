<?php

namespace App\Http\Controllers\LoanOfficer;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanOfficer\StoreCustomerRequest;
use App\Models\Loan\Customer;
use App\Models\Loan\TempCustomer;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class CustomerController extends Controller
{
    use Permissions;
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($this->isLoanOfficer($request->user()) || $this->isCashier($request->user())) {
            $branch_id = $request->user()->branch_id;

            $customer = Customer::where('branch_id', $branch_id)->with(['customerLoanRelation'])->get();
            //            return $this->success(['customer' => $customer], 'Customer Retrieved');
            $customer_filter = Customer::select(
                'customer_name',
                'customer_phone',
                'customer_email',
                'customer_gender',
                'customer_relation',
                'customer_residence',
                'nida_number',
                'guarantor_name',
                'guarantor_phone'
            )
                ->where('branch_id', $branch_id)
                ->with(['customerLoanRelation'])
                ->get();

            return $this->success(['customer' => $customer, 'customer_filter' => $customer_filter], 'Customer Retrieved');
        }

        return $this->error('', 'Unauthorized Access', 401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request): \Illuminate\Http\JsonResponse
    {

        if ($this->isLoanOfficer($request->user()) || $this->isCashier($request->user())) {

            $request->validated($request->all());
            $customer_img_storage = '/Customer_img/';
            $customer_file_img_Storage = $request->file('customer_img');
            $customer_img = $customer_img_storage . date('Y-m-d') . $customer_file_img_Storage->getClientOriginalName();
            $customer_file_img_Storage->storeAs('public', $customer_img);

            $customer_img_id_storage = '/Customer_img_id/';
            $customer_field_img_id = $request->file('customer_img_id');
            $customer_img_id = $customer_img_id_storage . date('Y-m-d') . $customer_field_img_id->getClientOriginalName();
            $customer_field_img_id->storeAs('public', $customer_img_id);

            $guarantor_photo_storage = '/Guarantor_photo/';
            $guarantor_photo_field = $request->file('guarantor_photo');
            $guarantor_photo = $guarantor_photo_storage . date('Y-m-d') . $guarantor_photo_field->getClientOriginalName();
            $guarantor_photo_field->storeAs('public', $guarantor_photo);

            $customer_exist = Customer::where('customer_email', $request->input('customer_email'))->where('nida_number', $request->input('nida_number'));

            $customer = Customer::updateOrCreate(
                [
                    'nida_number' => $request->input('nida_number'),
                    'customer_email' => $request->input('customer_email'),
                    'guarantor_name' => $request->input('guarantor_name'),
                    'guarantor_nida' => $request->input('guarantor_nida')

                ],
                [
                    'customer_img' => $customer_img,
                    'customer_img_id' => $customer_img_id,
                    'nida_number' => $request->input('nida_number'),
                    'customer_name' => $request->input('customer_name'),
                    'customer_email' => $request->input('customer_email'),
                    'customer_phone' => $request->input('customer_phone'),
                    'customer_gender' => $request->input('customer_gender'),
                    'customer_dob' => $request->input('customer_dob'),
                    'customer_relation' => $request->input('customer_relation'),
                    'customer_residence' => $request->input('customer_residence'),
                    'customer_guarantee' => $request->input('customer_guarantee'),
                    'guarantor_name' => $request->input('guarantor_name'),
                    'guarantor_gender' => $request->input('guarantor_gender'),
                    'guarantor_photo' => $guarantor_photo,
                    'guarantor_phone' => $request->input('guarantor_phone'),
                    'guarantor_nida' => $request->input('guarantor_nida'),
                    'branch_id' => $request->user()->branch_id,
                ]
            );
            return $this->success(['customer' => $customer], 'Customer Created With Loan Privilege');
        }
        return $this->error('', 'Unauthorized Access', 401);
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

    public function temporaryCustomers(Request $request)
    {
        $customer_name = $request->input('customer_name');
        $nida_number = $request->input('nida_number');
        $form_cost = $request->input('form_cost');
        $temp_customer = TempCustomer::where('nida_number', $nida_number);
        if ($temp_customer->count() > 0) {
            return $this->error('', 'Customer Already Exist', 429);
        } else {
            
            TempCustomer::create([
                'customer_name' => $customer_name,
                'nida_number' => $nida_number,
                'form_cost' => $form_cost,
            ]);
            
        }
    }
}
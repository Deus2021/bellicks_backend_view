<?php

namespace App\Http\Controllers\LoanOfficer;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanOfficer\StoreCustomerMessageRequest;
use App\Models\Loan\CustomerMessage;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;

class CustomerMessageController extends Controller
{
    use Permissions;
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($this->isLoanOfficer($request->user()) || $this->isCashier($request->user())) {
            $customer_message = CustomerMessage::all();
            return $this->success($customer_message);
        }
        return $this->error('', 'Unauthorized Access', 401);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     //
    // }

    public function store(StoreCustomerMessageRequest $request): \Illuminate\Http\JsonResponse
    {


        if ($this->isLoanOfficer($request->user()) || $this->isCashier($request->user())) {

            $request->validated($request->all());

            $customer_message = CustomerMessage::updateOrCreate(
                [
                    'message_title' => $request->input('message_title'),
                    // 'message_description' => $request->input('message_description')

                ],
                [
                    'message_title' => $request->input('message_title'),
                    'message_description' => $request->input('message_description'),
                    'customer_response' => 1,
                    'ussd_id' => 1,
                ]
            );
            return $this->success(['customer_message' => $customer_message], 'Customer
             message Created');
        }
        return $this->error('', 'Unauthorized Access', 401);
    }
    /**
     * Display the specified resource.
     */
    public function show(CustomerMessage $customerMessage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerMessage $customerMessage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerMessage $customerMessage)
    {
        //
    }
}

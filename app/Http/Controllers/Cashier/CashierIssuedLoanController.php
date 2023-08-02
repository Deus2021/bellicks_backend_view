<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Admin\Capital;
use App\Models\Loan\Customer;
use App\Models\Loan\Loan;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;

class CashierIssuedLoanController extends Controller
{
    use Permissions;
    use HttpResponses;

    public function index(Request $request)
    {
        if ($this->isCashier($request->user()))

            $customers = Customer::with(['customerBranchRelation', 'customerLoanRelation'])
                ->whereHas('customerBranchRelation', function ($query) use ($request) {
                    $query->where('branch_id', $request->user()->branch_id);
                })
                ->whereHas('customerLoanRelation', function ($query) {
                    $query->where('payment_status', 'unpaid');
                    $query->where('loan_status', 'approved');
                }) // Optional: If you only want customers with loans
                ->get();
        return $this->success($customers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $loan = Loan::find($request->id);

        if ($loan->count() > 0) {
            $loan->issue_status = "declined"; // "pending", "approved", "rejected
            $loan->save();
            $loans = Loan::with(['customersLoansRelation'])->get();
            return $this->success($loans);
        } else {
            return $this->error('', 'Loan not found', 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $loan = Loan::where('customer_id', $id)->with(['customersLoansRelation', 'loansLoanTypesRelation'])->get();
        return $this->success($loan);
    }
    public function waitIssueCashier(Request $request)
    {
        if ($this->isCashier($request->user())) {
            $loans = Loan::where("loan_status", "approved")
                ->where("issue_status", "Not issued")
                ->with(['customersLoansRelation','loansLoanTypesRelation'])
                ->get();
            return $this->success($loans);
        }
        return $this->error('', 'Unauthorized Access', 401);
    }

//     public function LoanIssued(Request $request)
//     {
//         if ($this->isCashier($request->user())) {
//             $branch_id = $request->user()->branch_id;
//             $amount = $request->loan_amount;
//             $pesa = intval($amount);
//
//             // Check if the loan is already issued
//             $loan = Loan::where("loan_id", $request->loan_id)->where("issue_status", "issued")->first();
//
//             if ($loan) {
//                 // Loan is already issued, return a message
//                 return $this->error('', 'Loan has already been issued', 403);
//             } else {
//                 // Loan is not issued, check the capital
//
//                 // Get the available capital for the branch
//                 $capitals = Capital::where("branch_id", $branch_id);
//                 $cap = intval($capitals->get("capital_amount")[0]->capital_amount);
//                 // return $this->success($cap >= $pesa, 'Loan issued successfully');
//                 if ($cap >= $pesa) {
//                     // Sufficient capital, update the loan status to "issued" and the capital amount
//                     $val = $cap - $pesa;
//                     Capital::where('branch_id', $branch_id)->update(['capital_amount' => $val]);
//                     Loan::where("loan_id", $request->loan_id)->update(['issue_status' => 'issued']);
//
//                     // Return success message
//                     return $this->success("", 'Loan issued successfully');
//                 }
//
//                 else {
//                     // Insufficient capital, return an error message
//                     return $this->error('', 'Insufficient funds', 403);
//                 }
//             }
//         }
//     }

public function LoanIssued(Request $request)
{
    if ($this->isCashier($request->user())) {
        $branch_id = $request->user()->branch_id;
        // $amount = $request->loan_amount;
        // $pesa = intval($amount);

        $loan = Loan::where("loan_id", $request->loan_id)->where("issue_status", "issued")->first();
        // $pesa = intval($loan->get("loan_amount")[0]->loan_amount);
        if ($loan) {
            return $this->error('', 'Loan has already been issued', 403);
        } else {
            $loan = Loan::where("loan_id", $request->loan_id);
            $pesa = intval($loan->get("loan_amount")[0]->loan_amount);

            $capitals = Capital::where("branch_id", $branch_id);
            $cap = intval($capitals->get("capital_amount")[0]->capital_amount);

            if ($cap >= $pesa) {

                $val = $cap-$pesa;
                Capital::where('branch_id', $branch_id)->update(['capital_amount' => $val]);
                Loan::where("loan_id", $request->loan_id)->update(['issue_status' => 'issued']);

                return $this->success($val, 'Loan issued successfully');

            } else {
                return $this->error('', 'In sufficient funds', 403);
            }
        }
    }
}


//
//     public function LoanIssued(Request $request)
//     {
//
//         if ($this->isCashier($request->user())) {
//             $branch_id = $request->user()->branch_id;
//             $loan = Loan::where("loan_id", $request->loan_id);
//
//             $capitals = Capital::with(['capitalBranchRelation'])->where("branch_id", '=', $branch_id)->get();
//
//
//             if ($loan->count() > 0) {
//
//                 $loan->update(['issue_status' => 'issued']);
//
//                 $loan = Loan::where('branch_id', $request->user()->branch_id)->with(['customersLoansRelation', 'loansLoanTypesRelation'])->get();
//                 return $this->success($loan);
//             } else {
//                 return $this->error('', 'Loan not found', 404);
//             }
//         }
//     }
//
//     public function LoanIssuedDecline(Request $request)
//     {
//         if ($this->isCashier($request->user())) {
//             $loan = Loan::where("loan_id", $request->loan_id);
//
//             if ($loan->count() > 0) {
//
//                 $loan->update(['issue_status' => 'declined']);
//
//                 $loan = Loan::where('branch_id', $request->user()->branch_id)->with(['customersLoansRelation', 'loansLoanTypesRelation'])->get();
//                 return $this->success($loan);
//
//             } else {
//                 return $this->error('', 'Loan not found', 404);
//             }
//         }
//         return $this->error('', 'Unauthorized Access', 401);
//     }

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

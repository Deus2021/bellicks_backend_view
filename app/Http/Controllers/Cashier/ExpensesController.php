<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cashier\AddExpensesRequest;
use App\Models\Cashier\Expense;
use App\Models\Cashier\Expenses;
use App\Models\User;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{

    use HttpResponses;
    use Permissions;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($this->isCashier($request->user())) {
            $branch_id=$request->user()->branch_id;
            $users_of_branch= User::where('branch_id', $branch_id)
            ->with('roleRelation')
            ->whereHas('roleRelation', function ($query) {
                $query->where('role', '!=', 'super-admin');
            })
            ->get();
            return $this->success($users_of_branch);

        }

        return $this->error('', 'Unauthorized Access', 401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddExpensesRequest $request)
    {
        //
        $request->validated($request->all());
        if ($this->isCashier($request->user())) {

            $amount = $request->input('amount');
            $description = $request->input('description');
            $branch_id=$request->user()->branch_id;
            $expenses = Expense::create([
                'amount' => $amount,
                'description' => $description,
                'user_id' => $request->input('rank'),
                'branch_id' =>$branch_id
            ]);
            return $this->success(['expenses',$expenses]);
        }

        //
        //
        // return $this->error('','Unauthorized Access',401);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $ids)
    {
        $expenses = Expense::where('user_id',$ids)->with(['UserExpensesRelation'])->get();
        return $this->success($expenses);
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

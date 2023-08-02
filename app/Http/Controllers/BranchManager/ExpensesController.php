<?php

namespace App\Http\Controllers\BranchManager;

use App\Http\Controllers\Controller;
use App\Models\Cashier\Expense;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{

    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = Expense::with(['UserExpensesRelation'])->get();
        return $this->success($expenses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $expense = Expense::find($request->id);

        if ($expense->count() > 0) {
            $expense->status = "declined"; // "pending", "approved", "rejected
            $expense->save();
            $expenses = Expense::with(['UserExpensesRelation'])->get();
            return $this->success($expenses);
        } else {
            return $this->error('', 'expense not found', 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expense = Expense::find($id);

        if ($expense->count() > 0) {
            $expense->status = "approved"; // "pending", "approved", "rejected
            $expense->save();
            $expenses = Expense::with(['UserExpensesRelation'])->get();
            return $this->success($expenses);
        } else {
            return $this->error('', 'expense not found', 404);
        }
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

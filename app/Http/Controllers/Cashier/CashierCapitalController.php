<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Admin\Capital;
use App\Traits\HttpResponses;
use App\Traits\Permissions;
use Illuminate\Http\Request;

class CashierCapitalController extends Controller
{
    use HttpResponses;
    use Permissions;
    public function index()
    {
        $capitals = Capital::with(['capitalBranchRelation'])->get();
        return $this->success($capitals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
//          $branch_id = $request->user()->branch_id;
//          $amount=$request->loan_amount;
//          $pesa = intval($amount);
//
//          $capitals = Capital::where("branch_id",$branch_id);
//          $cap=intval($capitals->get("capital_amount")[0]->capital_amount);
//
//          if ($cap >= $pesa) {
//             $val=$cap-$pesa;
//             Capital::where('branch_id', $branch_id)->update(['capital_amount' => $val]);
//          }
//          else
//          {
//             return $this->success("",'Issuficent capital');
//          }

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

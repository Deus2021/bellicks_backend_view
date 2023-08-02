<?php

namespace App\Http\Controllers\BranchManager;

use App\Http\Controllers\Controller;
use App\Http\Requests\BranchManager\StoreInvetoryRequest;
use App\Models\BranchManager\Inventory;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Traits\Permissions;

class InventoryController extends Controller
{
    use HttpResponses;
    use Permissions;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($this->isBranchManager($request->user())) {
            $inventory = Inventory::all();
            return $this->success($inventory);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvetoryRequest $request)
    {
        $request->validated($request->all());
        if ($this->isBranchManager($request->user())) {
            $branch_id = $request->user()->branch_id;
            $inventory = Inventory::create([
                'branch_id' => $branch_id,
                'inventory_name' => $request->input('inventory_name'),
                'inventory_number' => $request->input('inventory_number'),
                'inventory_price' => $request->input('inventory_price'),
                'inventory_desc' => $request->input('inventory_desc'),
                'serial_no' => $request->input('serial_no'),
                'inventory_status' => $request->input('inventory_status'),
                'DOR' => $request->input('DOR'),
                'user_id' => $request->user()->user_id
            ]);
            return response()->json(['inventory' => $inventory, 'message' => 'Inventory created']);
        } else {
            return $this->error('', 'Unauthorized Access', 401);
        }
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

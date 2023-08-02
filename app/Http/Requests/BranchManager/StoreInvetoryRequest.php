<?php

namespace App\Http\Requests\BranchManager;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvetoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'inventory_name' => 'required|string|max:255',
            'branch_name' => 'required|string|max:255',
            'serial_no' => 'required|string|max:50',
            'inventory_number' => 'required|string|max:50',
            'inventory_price' => 'required|numeric|min:0',
            'DOR' => 'required|date_format:Y/m/d',
            'inventory_desc' => 'required|string',
            'inventory_status' => 'required|string|max:50',
        ];
    }
}

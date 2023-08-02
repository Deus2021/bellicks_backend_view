<?php

namespace App\Http\Requests\BranchManager;

use Illuminate\Foundation\Http\FormRequest;

class StoreToBankRequest extends FormRequest
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
            'bank_name' => 'required|string|max:255',
            'account_no' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
        ];
    }
}

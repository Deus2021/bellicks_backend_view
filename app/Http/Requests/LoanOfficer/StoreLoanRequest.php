<?php

namespace App\Http\Requests\LoanOfficer;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
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
            'loan_amount' => 'required|numeric',
            'form_cost' => 'required|numeric',
            'loan_type_id' => 'required|integer|max:255',
            'rate_amount' => 'required|string|max:255',
            'insurance_amount' => 'required|string|max:255',
        
        ];
    }
}
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanTypeRequest extends FormRequest
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
            "loan_type" => 'required|string|max:255',
            "desc" => 'required|string|max:255',
            "insurance" => 'required',
            "duration" => 'required',
            "rate" => 'required',
            "fixed_penalty" => 'required',
            "penalty_percentage" => 'required'
        ];
    }
}

// 'required|numeric|min:0'
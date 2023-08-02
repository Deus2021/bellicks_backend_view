<?php

namespace App\Http\Requests\LoanOfficer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'customer_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'customer_img_id' =>
            'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'nida_number' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|unique:customers|max:255',
            'customer_phone' => 'required|string|max:255',
            'customer_gender' => 'required|string|max:255',
            'customer_dob' => 'required|date',
            'customer_residence' => 'required|string|max:255',
            'customer_relation' => 'required|string|max:255',
            // 'customer_guarantee' => 'required|string|max:255',
            // 'guarantor_name' => 'required|string|max:255',
            // 'guarantor_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'guarantor_gender' => 'required|string|max:255',
            // 'guarantor_nida' => 'required|string|max:255',
            // 'guarantor_phone' => 'required|string|max:255',
        ];
    }
}
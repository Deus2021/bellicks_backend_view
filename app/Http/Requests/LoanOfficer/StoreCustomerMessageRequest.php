<?php

namespace App\Http\Requests\LoanOfficer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerMessageRequest extends FormRequest
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
            'message_title' => 'required|string|max:255',
            'message_description' => 'required|string|max:255',
            // 'customer_response' => 'required|string|max:255',
            // 'ussd_id' => 'required|integer',
        ];
    }
}

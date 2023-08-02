<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8', // |confirmed
            'phone' => 'required|string|max:20',
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_type' => 'required|string|max:255',
            'id_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_number' => 'required|string|max:255',
            'DOB' => 'required|date',
            'employement_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'branch_id' => 'required|integer',
        ];
    }
}

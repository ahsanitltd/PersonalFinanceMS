<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobEarningRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_id' => 'required|numeric',
            'amount' => 'required|numeric',
            'currency' => 'required|string',
            'earn_month' => 'nullable',
            'is_paid' => 'nullable',
            'paid_at' => 'nullable',
            'notes' => 'nullable',
        ];
    }
}

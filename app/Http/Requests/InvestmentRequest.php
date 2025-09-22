<?php

namespace App\Http\Requests;

use App\Traits\IsValidRequest;
use Illuminate\Foundation\Http\FormRequest;

class InvestmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    use IsValidRequest;

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
            "investment_partner_id" => 'required|numeric',
            "agreed_amount" => 'required|numeric',
            "amount_invested" => 'required|numeric',
            "your_due" => 'nullable|numeric',
            "partner_due" => 'nullable|numeric',
            "profit_type" => 'required|in:percentage,fixed',
            "profit_value" => 'required|numeric',
            "notes" => 'nullable|string',
        ];
    }
}

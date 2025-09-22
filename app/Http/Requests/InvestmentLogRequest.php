<?php

namespace App\Http\Requests;

use App\Traits\IsValidRequest;
use Illuminate\Foundation\Http\FormRequest;

class InvestmentLogRequest extends FormRequest
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
            "investment_id" => 'required|numeric',
            'type' => 'required|in:investment,partner_investment,due_payment,profit,loss,return,note',
            "paid_by" => 'required|numeric',
            "amount" => 'required|numeric',
            "note" => 'nullable|string',
        ];
    }
}

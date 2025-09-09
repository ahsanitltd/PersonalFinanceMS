<?php

namespace App\Http\Requests;

use App\Traits\IsValidRequest;
use Illuminate\Foundation\Http\FormRequest;

class InvestmentEntityRequest extends FormRequest
{
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
            'name' => 'required|string|max:255',
            'type' => 'required|in:individual,company,stock,crypto,real_estate,deal',
            'contact' => 'required|string',
            'description' => 'nullable|string'
        ];
    }
}

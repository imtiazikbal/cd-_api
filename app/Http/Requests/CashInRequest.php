<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CashInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'ledger_id'    => ['required'],
            'amount'       => ['required'],
            'payor_id'     => ['required'],
            'payment_type' => ['required'],
        ];
    }
}

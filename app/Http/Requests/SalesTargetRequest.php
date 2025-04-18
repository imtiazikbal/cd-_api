<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;

class SalesTargetRequest extends FormRequest
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
            'daily'     => 'integer',
            'monthly'   => 'integer',
            'custom'    => 'integer',
            'from_date' => Rule::requiredIf(function () {
                return $this->request->get('custom');
            }),
            'to_date' => Rule::requiredIf(function () {
                return $this->request->get('custom');
            }),
        ];
    }
}

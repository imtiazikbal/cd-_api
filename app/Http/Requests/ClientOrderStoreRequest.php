<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientOrderStoreRequest extends FormRequest
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
            'customer_name'    => ['required', 'string', 'max:50'],
            'customer_phone'   => ['required', 'string', 'max:14'],
            'customer_address' => ['required', 'string', 'min:10', 'max:120'],
            'product_id.*'     => ['required', 'integer', 'min:1'],
            'product_qty.*'    => ['required', 'integer', 'min:1'],
            'order_attach_img.*'=> 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ];
    }
}
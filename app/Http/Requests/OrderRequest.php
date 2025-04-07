<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Request;

class OrderRequest extends FormRequest
{
    protected $orderRequireValidation = 'required|string|max:255';

    protected $orderRequireValidationTwo = 'required|array|min:1';

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
        if (Request::route()->getName() === "client.orders.update") {
            return [
                'customer_name'    => $this->orderRequireValidation,
                'customer_phone'   => $this->orderRequireValidation,
                'customer_address' => $this->orderRequireValidation,
                'product_id'       => $this->orderRequireValidationTwo,
                'product_id.*'     => 'required|integer|distinct|min:1',
                'product_qty'      => $this->orderRequireValidationTwo,
            ];
        }

        if (Request::route()->getName() === "client.orders.status.update") {
            return [
                'order_id' => 'required|integer',
                'status'   => $this->orderRequireValidation,
            ];
        }

        if (Request::route()->getName() === "client.product.return.update") {
            return [
                'order_id'          => 'required|integer',
                'return_order_note' => $this->orderRequireValidation,
            ];
        }

        return [];
    }

    public function failedValidation(Validator $validator)
    {
        throw new   HttpResponseException(response()->json(
            [
                'success' => false,
                'msg'     => $validator->errors(),
            ],
            400
        ));
    }
}

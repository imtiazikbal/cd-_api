<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Request;

class ProductRequest extends FormRequest
{
    protected $validationString = 'required|string';

    protected $validationInteger = 'required|integer';

    protected $validationNullableString = 'nullable|string';

    protected $validationNullableInteger = 'nullable|integer';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    // public function authorize()
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (Request::route()->getName() === "client.products.store") {
            return [
                'category_id'       => 'required_without:category_name|integer',
                'category_name'     => 'required_without:category_id|string',
                'product_name'      => $this->validationString,
                'product_code'      => $this->validationString,
                'price'             => $this->validationInteger,
                'discount'          => $this->validationInteger,
                'short_description' => $this->validationNullableString,
                'main_image'        => 'required|image|mimes:png,jpg,jpeg',
                'status'            => 'nullable|boolean',
            ];
        }

        if (Request::route()->getName() === "client.inventory.update") {
            return [
                'product_id'    => $this->validationInteger,
                'product_name'  => $this->validationNullableString,
                'product_code'  => $this->validationNullableString,
                'selling_price' => $this->validationNullableInteger,
                'main_image'    => 'nullable|image|mimes:png,jpg,jpeg',
            ];
        }

        if (Request::route()->getName() === "client.stock.in.update") {
            return [
                'product_id'     => $this->validationInteger,
                'stock_quantity' => $this->validationInteger,
            ];
        }

        if (Request::route()->getName() === "product.return.update") {
            return [
                'product_id'           => $this->validationInteger,
                'renturn_product_note' => $this->validationNullableInteger,
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

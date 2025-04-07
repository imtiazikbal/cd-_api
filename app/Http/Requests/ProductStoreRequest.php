<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
            'category_id'       => 'required_without:category_name|integer',
            'category_name'     => 'required_without:category_id|string',
            'product_name'      => 'required|string',
            'price'             => 'required|integer',
            'discount'          => 'required|integer',
            'short_description' => 'nullable|string',
            'main_image'        => 'required|image|mimes:png,jpg,jpeg|max:5120',
            'gallery_image'     => 'nullable|array|max:5',
            'gallery_image.*'   => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
            'status'            => 'nullable|boolean',
        ];
    }
}
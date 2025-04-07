<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingSettingStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'inside'  => ['nullable'],
            'outside' => ['nullable'],
            'subarea' => ['nullable'],
            'shop_id' => ['nullable'],
            'status'  => ['nullable'],
        ];
    }
}

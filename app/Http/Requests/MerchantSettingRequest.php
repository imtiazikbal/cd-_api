<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class MerchantSettingRequest extends FormRequest
{
    protected $shopRequireValidation = 'required|string|max:255';

    protected $shopNullableValidation = 'nullable|string|max:255';

    protected $shopNullableStringValidation = 'nullable|string';

    protected $validationNullableInteger = 'nullable|integer';

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
        if (\Request::route()->getName() === "client.settings.business.info.update") {

            return [
                'shop_name'             => $this->shopRequireValidation,
                'shop_address'          => $this->shopNullableValidation,
                'shop_logo'             => 'nullable|image',
                'shop_id'               => $this->validationNullableInteger,
                'shop_meta_title'       => $this->shopNullableStringValidation,
                'shop_meta_description' => $this->shopNullableStringValidation,
            ];
        }

        if (\Request::route()->getName() === "client.settings.owner.info.update") {

            return [
                'owner_name'       => $this->shopRequireValidation,
                'owner_number'     => $this->shopRequireValidation,
                'owner_email'      => $this->shopRequireValidation,
                'owner_address'    => $this->shopNullableValidation,
                'owner_other_info' => $this->shopNullableValidation,
            ];
        }

        if (\Request::route()->getName() === "client.settings.password.security.update") {

            return [
                'old_password'          => 'required',
                'new_password'          => 'required|min:6|same:password_confirmation',
                'password_confirmation' => 'required|min:6'
            ];
        }

        if (\Request::route()->getName() === "client.settings.website.update") {

            return [
                'cash_on_delivery'  => 'nullable|boolean',
                'invoice_id'        => $this->validationNullableInteger,
                'custom_domain'     => $this->shopNullableStringValidation,
                'shop_name'         => $this->shopNullableValidation,
                'shop_address'      => $this->shopNullableValidation,
                'website_shop_logo' => 'nullable|image',
                'shop_id'           => $this->validationNullableInteger,
                'meta_title'        => $this->shopNullableStringValidation,
                'meta_description'  => $this->shopNullableStringValidation,

            ];
        }

        return [];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            "success" => false,
            "errors"  => $validator->errors(),
            "msg"     => $validator->messages("*")->first()
        ], 400));
    }
}

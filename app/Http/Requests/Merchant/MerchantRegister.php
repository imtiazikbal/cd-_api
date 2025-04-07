<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class MerchantRegister extends FormRequest
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
            'name'      => ['required'],
            'email'     => ['required', Rule::unique('users')],
            'phone'     => ['required', Rule::unique('users')],
            'password'  => ['required', 'confirmed', Password::default()],
            'shop_name' => ['required']
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StaffStoreRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name'     => ['required', 'min:6'],
            'email'    => ['required', Rule::unique('users', 'email')->ignore($this->id)],
            'phone'    => ['required', Rule::unique('users', 'phone')->ignore($this->id)],
            'password' => ['required', Password::default()],
            'role'     => ['required'],
        ];
    }
}

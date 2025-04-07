<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Request;

class SliderRequest extends FormRequest
{
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
        if(Request::route()->getName() === "client.sliders.store") {
            return [
                'title'       => 'required|string|max:255',
                'description' => 'required|string',
                'image'       => 'required|image|mimes:png,jpg,jpeg|max:2048',
            ];
        }

        if(Request::route()->getName() === "client.sliders.update") {
            return [
                'title'       => 'required|string|max:255',
                'description' => 'required|string',
                'image'       => 'required|image|mimes:png,jpg,jpeg|max:2048',
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

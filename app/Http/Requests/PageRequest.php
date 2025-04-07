<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Request;

class PageRequest extends FormRequest
{
    protected $pageRequireValidation = 'required|integer';

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
        if (Request::route()->getName() === "client.page.store") {
            return [
                'title' => 'required|string|max:255',
                // 'theme' => $this->pageRequireValidation,
                // 'status' => $this->pageRequireValidation,
                'page_content' => 'nullable',
            ];
        }

        if (Request::route()->getName() === "client.page.update") {
            return [
                'title' => 'required|string|max:255',
                // 'theme' => $this->pageRequireValidation,
                // 'status' => $this->pageRequireValidation,
                'page_content' => 'nullable',
            ];
        }


        return [];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(
            [
                'success' => false,
                'msg'     => $validator->errors(),
            ],
            400
        ));
    }
}

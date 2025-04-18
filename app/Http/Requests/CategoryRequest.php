<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name'           => 'required|string|max:100',
            'parent_id'      => 'nullable|integer',
            'category_image' => 'nullable|image|mimes:png,jpg,jpeg|max:10240',
            'status'         => 'required|integer'
        ];
    }
}

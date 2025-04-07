<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupportTicketStoreRequest extends FormRequest
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
            'subject'    => ['required', 'min:6'],
            'content'    => ['required', 'min:20'],
            'user_id'    => ['required'],
            'attachment' => ['nullable', 'mimes:jpg,bmp,png,pdf,jpeg'],
        ];
    }
}

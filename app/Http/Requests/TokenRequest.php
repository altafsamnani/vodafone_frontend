<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TokenRequest extends FormRequest
{
    /** Get the validation rules that apply to the request */
    public function rules(): array
    {
        return [
            'refresh_token' => 'required',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthenticateRequest extends FormRequest
{
    /** Get the validation rules that apply to the request. */
    public function rules(): array
    {
        return [
            'vodafone_url' => 'required|url',
            'scope' => 'required|array',
            'vodafone_session' => 'required|string',
            'exchange_verifier_challenge' => 'required|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $url = $this->get('vodafone_url');
        parse_str(parse_url($url)['query'], $params);
        $params['scope'] = explode(' ', urldecode($this->request->get('scope')));
        $this->merge($params);
    }
}

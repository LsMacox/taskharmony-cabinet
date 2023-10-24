<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard()->check();
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['nullable', 'string', 'max:255'],
            'password' => ['string', 'max:255'],
        ];

        if ($this->method() === 'POST') {
            $rules['email'][] = ['required', 'email'];
        }

        return $rules;
    }
}

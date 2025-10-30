<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'identity_num' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'identity_num')->ignore($this->user()->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user()->id),
            ],
            'phone_number' => [
                'required',
                'string',
                'max:20',
                'regex:/^(?:\+?62|62|0)8[1-9][0-9]{6,11}$/',
                Rule::unique('users', 'phone_number')->ignore($this->user()->id),
            ],
            'birthplace'   => ['required', 'string', 'max:100'],
            'birthdate'    => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'address'      => ['required', 'string', 'max:1000'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\ValidateCpf;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            '*' => 'required',
            'password' => 'min:8',
            'email' => ['email', 'unique:users'],
            'role' => [Rule::in(['default', 'lojista'])],
            'cpf' => [new ValidateCpf(), 'unique:users']
        ];
    }

    public function messages()
    {
        return [
            'role.in' => 'The role field must be either default or lojista.',
        ];
    }
}

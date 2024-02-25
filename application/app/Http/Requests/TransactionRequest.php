<?php

namespace App\Http\Requests;

use App\Rules\NotEqualIds;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'value'=>['required', 'decimal:0,2'],
            'sender_id'=>['required', 'exists:users,id'],
            'receiver_id'=>['required', 'exists:users,id', new NotEqualIds()],
        ];
    }
}

<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class DenyLojista implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        // Verificar se o usuário é um lojista
        $user = User::find($value); // Obter o usuário com base no ID
        return $user && $user->role !== 'lojista'; // Retorna true se o usuário não for lojista
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Employees are not allowed to send funds.';
    }
}
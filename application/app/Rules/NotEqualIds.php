<?php
// app/Rules/NotEqual.php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NotEqualIds implements Rule
{
    public function passes($attribute, $value)
    {
        $senderId = request()->get('sender_id');
        return $value !== $senderId;
    }

    public function message()
    {
        return 'The user_id should not be equal to receiver_id';
    }
}

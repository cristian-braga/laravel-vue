<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => $this->passwordRules(),
        ], [
            'current_password.required' => __('O campo senha atual é obrigatório.'),
            'current_password.current_password' => __('A senha fornecida não corresponde à sua senha atual.'),
            'password.required' => __('O campo nova senha é obrigatório.'),
            'password.confirmed' => __('A confirmação de senha não corresponde.'),
            'password.min' => __('A senha deve ter pelo menos :min caracteres.')
        ])->validateWithBag('updatePassword');

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}

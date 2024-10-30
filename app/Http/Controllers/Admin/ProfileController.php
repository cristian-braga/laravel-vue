<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Fortify\UpdateUserPassword;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->only(['name', 'email', 'role', 'avatar']);

        return $user;
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = request()->validate([
            'name' => 'required',
            'email' => "required|unique:users,email, {$user->id}",
            // 'password' => 'required|min:8'
        ], [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.unique' => 'Esse e-mail já está em uso. Por favor, escolha outro.',
            // 'password.min' => 'A senha deve ter pelo menos :min caracteres.'
        ]);

        $user->update($data);

        return response()->json(['success' => true]);
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('profile_picture')) {
            $previousPath = $request->user()->getRawOriginal('avatar');

            $link = Storage::disk('public')->put('/photos', $request->file('profile_picture'));

            $request->user()->update(['avatar' => $link]);

            if ($previousPath && $previousPath !== 'noimage.jpg') {
                Storage::disk('public')->delete($previousPath);
            }

            return response()->json(['message' => 'Foto de Perfil carregada com sucesso!']);
        }
    }

    public function changePassword(Request $request, UpdateUserPassword $updater)
    {
        $updater->update(
            auth()->user(),
            [
                'current_password' => $request->currentPassword,
                'password' => $request->password,
                'password_confirmation' => $request->passwordConfirmation
            ]
        );

        return response()->json(['message' => 'Senha alterada com sucesso!']);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // $users = User::latest()->get()->map(function ($user) {
        //     return [
        //         'id' => $user->id,
        //         'name' => $user->name,
        //         'email' => $user->email,
        //         'created_at' => $user->created_at->format('d/m/Y')
        //     ];
        // });

        $search = $request->query('query');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })    
            ->latest()
            ->paginate();

        return $users;
    }

    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8'
        ], [
            'name.required' => 'O campo nome é obrigatório.',
            'email.unique' => 'Esse e-mail já está em uso. Por favor, escolha outro.',
            'password' => 'A senha deve ter pelo menos :min caracteres.'
        ]);

        $data = $request->all();

        $data['password'] = bcrypt($request['password']);

        $user = User::create($data);

        return $user;
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'name' => 'required',
            'email' => "required|unique:users,email,{$id}",
            'password' => 'sometimes|min:8'
        ], [
            'name.required' => 'O campo nome é obrigatório.',
            'email.unique' => 'Esse e-mail já está em uso. Por favor, escolha outro.',
            'password' => 'A senha deve ter pelo menos :min caracteres.'
        ]);

        $user = User::find($id);

        $data = $request->all();

        $data['password'] = $request['password'] ? bcrypt($request['password']) : $user->password;

        $user = $user->update($data);

        return $user;
    }

    public function changeRole(Request $request, $id)
    {
        $user = User::find($id);

        $user->update([
            'role' => $request['role']
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();

            return response()->noContent();
        } else {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }
    }

    public function bulkDelete(Request $request)
    {
        User::whereIn('id', $request['ids'])->delete();

        return response()->json(['message' => 'Usuário(s) excluído(s) com sucesso!']);
    }
}

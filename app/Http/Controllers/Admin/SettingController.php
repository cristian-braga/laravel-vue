<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return $settings;
    }

    public function update(Request $request)
    {
        $settings = request()->validate([
            'app_name' => 'required|string',
            'date_format' => 'required|string',
            'pagination_limit' => 'required|integer|between:1,100'
        ], [
            'app_name.required' => 'O campo Nome de exibição é obrigatório.',
            'date_format.required' => 'O campo Formato de datas é obrigatório.',
            'pagination_limit.required' => 'O campo Número de items exibidos é obrigatório.',
            'pagination_limit.integer' => 'O campo Número de items precisa ser um número inteiro.',
            'pagination_limit.between' => 'O campo Número de itens deve estar entre 1 e 100.'
        ]);

        foreach ($settings as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }

        return response()->json(['success' => true]);
    }
}

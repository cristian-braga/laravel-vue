<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {

        $appointments = Appointment::query()
            ->with('client:id,first_name,last_name')
            ->when(request('status'), function ($query) {
                return $query->where('status', AppointmentStatus::from(request('status')));
            })
            ->latest()
            ->paginate()
            ->through(fn($appointment) => [
                'id' => $appointment->id,
                'start_time' => $appointment->start_time->format('d/m/Y H:i'),
                'end_time' => $appointment->end_time->format('d/m/Y H:i'),
                'status' => [
                    'name' => $appointment->status->name,
                    'color' => $appointment->status->color()
                ],
                'client' => $appointment->client
            ]);

        return $appointments;
    }

    public function getStatusWithCount()
    {
        $cases = AppointmentStatus::cases();

        return collect($cases)->map(function ($status) {
            return [
                'name' => $status->name,
                'value' => $status->value,
                'count' => Appointment::where('status', $status->value)->count(),
                'color' => AppointmentStatus::from($status->value)->color()
            ];
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'client_id' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'description' => 'required',
        ], [
            'title.required' => 'O campo Título é obrigatório.',
            'client_id.required' => 'O campo Cliente é obrigatório.',
            'start_time.required' => 'O campo Início é obrigatório.',
            'end_time.required' => 'O campo Fim é obrigatório.',
            'description.required' => 'O campo Descrição é obrigatório.'
        ]);

        Appointment::create([
            'title' => $validated['title'],
            'client_id' => $validated['client_id'],
            'start_time' => Carbon::createFromFormat('d/m/Y H:i', $validated['start_time']),
            'end_time' => Carbon::createFromFormat('d/m/Y H:i', $validated['end_time']),
            'description' => $validated['description'],
            'status' => AppointmentStatus::AGENDADO
        ]);
    }

    public function edit($id)
    {
        $appointment = Appointment::find($id);

        return $appointment;
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::find($id);

        $validated = $request->validate([
            'title' => 'required',
            'client_id' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'description' => 'required',
        ], [
            'title.required' => 'O campo Título é obrigatório.',
            'client_id.required' => 'O campo Cliente é obrigatório.',
            'start_time.required' => 'O campo Início é obrigatório.',
            'end_time.required' => 'O campo Fim é obrigatório.',
            'description.required' => 'O campo Descrição é obrigatório.'
        ]);

        $appointment = $appointment->update([
            'title' => $validated['title'],
            'client_id' => $validated['client_id'],
            'start_time' => Carbon::createFromFormat('d/m/Y H:i', $validated['start_time']),
            'end_time' => Carbon::createFromFormat('d/m/Y H:i', $validated['end_time']),
            'description' => $validated['description']
        ]);

        return $appointment;
    }

    public function destroy($id)
    {
        $appointment = Appointment::find($id);

        if ($appointment) {
            $appointment->delete();

            return response()->noContent();
        } else {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }
    }
}

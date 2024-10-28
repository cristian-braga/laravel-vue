<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardStatsController extends Controller
{
    public function appointments()
    {
        $total_appointments_count = Appointment::query()
            ->when(request('status') === 'agendado', function ($query) {
                $query->where('status', AppointmentStatus::AGENDADO);
            })
            ->when(request('status') === 'confirmado', function ($query) {
                $query->where('status', AppointmentStatus::CONFIRMADO);
            })
            ->when(request('status') === 'cancelado', function ($query) {
                $query->where('status', AppointmentStatus::CANCELADO);
            })
            ->count();

        return response()->json(['totalAppointmentsCount' => $total_appointments_count]);
    }

    public function users()
    {
        $total_users_count = User::query()
            ->when(request('date_range') === 'today', function ($query) {
                $query->whereBetween('created_at', [now()->today(), now()]);
            })
            ->when(request('date_range') === '30_days', function ($query) {
                $query->whereBetween('created_at', [now()->subDays(30), now()]);
            })
            ->when(request('date_range') === '60_days', function ($query) {
                $query->whereBetween('created_at', [now()->subDays(60), now()]);
            })
            ->when(request('date_range') === 'year', function ($query) {
                $query->whereBetween('created_at', [now()->subYear(1), now()]);
            })
            ->when(request('date_range') === 'month_to_date', function ($query) {
                $query->whereBetween('created_at', [now()->firstOfMonth(), now()]);
            })
            ->when(request('date_range') === 'year_to_date', function ($query) {
                $query->whereBetween('created_at', [now()->firstOfYear(), now()]);
            })
            ->count();

        return response()->json(['totalUsersCount' => $total_users_count]);
    }
}

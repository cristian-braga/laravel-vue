<?php

namespace App\Enums;

enum AppointmentStatus: int
{
    case AGENDADO = 1;
    case CONFIRMADO = 2;
    case CANCELADO = 3;

    public function color(): string
    {
        return match($this) {
            AppointmentStatus::AGENDADO => 'primary',
            AppointmentStatus::CONFIRMADO => 'success',
            AppointmentStatus::CANCELADO => 'danger'
        };
    }
}

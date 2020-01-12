<?php

namespace App\Appointment\View;

use App\Appointment\Entity\Appointment;

final class StreamItem
{
    public int $length;

    public Appointment $appointment;

    public function __construct(int $length, Appointment $appointment)
    {
        $this->length = $length;
        $this->appointment = $appointment;
    }
}

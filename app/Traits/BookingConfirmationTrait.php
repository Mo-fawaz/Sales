<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmedMail;
use App\Notifications\BookingConfirmed;
use Illuminate\Support\Facades\Notification;

trait BookingConfirmationTrait
{
    public function confirmBooking($booking)
    {
        if ($booking->status !== 'paid') {
            return false;
        }
        $ticketNumber = 'TICKET-' . strtoupper(Str::random(10));
        $booking->update([
            'status' => 'confirmed',
            'ticket_number' => $ticketNumber,
        ]);
        $passengers = $booking->passengers;
        foreach ($passengers as $passenger) {
            $passenger->notify(new BookingConfirmed($passenger));
        }

        return true;
    }

    public function cancelBooking($booking)
    {
        $booking->update([
            'status' => 'canceled',
            'ticket_number' => null,
        ]);

        return true;
    }
}

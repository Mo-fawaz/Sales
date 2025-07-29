<?php

namespace App\Notifications;

use App\Models\FlightBooking;
use App\Models\FlightPassenger;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification
{
    use Queueable;

    public FlightPassenger $passenger;

    public function __construct(FlightPassenger $passenger)
    {
        $this->passenger = $passenger;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $passenger = $this->passenger;
        $booking = $passenger->booking;

        return (new MailMessage)
            ->subject('Your booking has been confirmed 🚀')
            ->greeting('Hello ' . $passenger->first_name)
            ->line('Thank you for using our service. Your flight booking has been successfully confirmed.')
            ->line('🎫 Ticket Number: ' . $booking->ticket_number)
            ->line('Booking Reference: ' . $booking->reference)
            ->line('From: ' . $booking->origin)
            ->line('To: ' . $booking->destination)
            ->line('Departure: ' . $booking->departure_time)
            ->line('Arrival: ' . $booking->arrival_time)
            ->line('Airline: ' . $booking->airline)
            ->line('Total Price: €' . $booking->total_price)
            ->line('We wish you a pleasant journey! ✈️')
            ->salutation('Best regards, Travelx Team');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FlightBooking;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Stripe\Charge;
use Stripe\Stripe;

class StripeController extends Controller
{
    public function index()
    {
        return view('checkout');
    }
    public function createCharge(Request $request)
    {
        $request->validate([
            'stripeToken' => 'required|string',
            'booking_id' => 'required|exists:flight_bookings,id',
        ]);
        $booking = FlightBooking::findOrFail($request->booking_id);
        DB::beginTransaction();
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $charge = Charge::create([
                "amount" => $booking->total_price * 100,
                "currency" => "eur",
                "source" => $request->stripeToken,
                "description" => "Flight booking payment for Order ID: " . $booking->order_id,
            ]);
            $payment = Payment::create([
                'flight_booking_id' => $booking->id,
                'charge_id' => $charge->id,
                'transaction_id' => $charge->balance_transaction,
                'amount' => number_format(($charge->amount) / 100, 2),
                'card_id' => $charge->source->id,
                'card_last_four' => $charge->source->last4,
                'card_exp_month' => $charge->source->exp_month,
                'card_exp_year' => $charge->source->exp_year,
                'postal_code' => $charge->source->address_zip,
            ]);
            $booking->update([
                'status' => 'paid',
                //'ticket_number' => 'TKT' . strtoupper(\Str::random(8)),
            ]);
            DB::commit();
            // Notification::route('mail', $booking->email)->notify(new BookingConfirmed($booking));
            return response()->json([
                'message' => 'Payment successful, ticket issued!',
                'ticket_number' => $booking->ticket_number,
                'booking' => $booking,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Payment failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}

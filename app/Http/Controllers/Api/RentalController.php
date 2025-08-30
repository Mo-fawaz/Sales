<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    // تقديم طلب إيجار
    public function store(Request $request, Car $car)
    {
        $data = $request->validate([
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'pickup_location' => 'nullable|string|max:255',
        ]);

        if ($car->status !== 'approved') {
            return response()->json(['message' => 'Car not available for rent'], 422);
        }

        $estimatedPrice = 0;

        if ($car->pricing_type === 'per_day') {
            if (!empty($data['start_date']) && !empty($data['end_date'])) {
                $days = \Carbon\Carbon::parse($data['start_date'])
                        ->diffInDays(\Carbon\Carbon::parse($data['end_date'])) + 1;
                $estimatedPrice = $days * $car->price;
            }
        } elseif ($car->pricing_type === 'fixed') {
            $estimatedPrice = $car->price;
        } elseif ($car->pricing_type === 'per_hour') {
            if (!empty($data['start_date']) && !empty($data['end_date'])) {
                $hours = \Carbon\Carbon::parse($data['start_date'])
                    ->diffInHours(\Carbon\Carbon::parse($data['end_date']));
                $estimatedPrice = $hours * $car->price;
            }
        }

        $rental = Rental::create([
            'car_id'          => $car->id,
            'renter_id'       => auth('sanctum')->id(),
            'start_date'      => $data['start_date'] ?? null,
            'end_date'        => $data['end_date'] ?? null,
            'pickup_location' => $data['pickup_location'] ?? null,
            'status'          => 'pending',
            'estimated_price' => $estimatedPrice,
        ]);

        return response()->json([
            'message' => 'Rental request submitted',
            'data'    => $rental
        ]);
    }

    // قبول الطلب (من صاحب السيارة فقط)
    public function accept(Rental $rental)
    {
        if ($rental->car->owner_id !== auth()->id()) {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        if ($rental->status !== 'pending') {
            return response()->json(['message'=>'Request already processed'],422);
        }

        $rental->update(['status'=>'accepted']);

        return response()->json([
            'message'=>'Rental request accepted',
            'data'=>$rental
        ]);
    }

    // رفض الطلب
    public function reject(Rental $rental)
    {
        if ($rental->car->owner_id !== auth()->id()) {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        if ($rental->status !== 'pending') {
            return response()->json(['message'=>'Request already processed'],422);
        }

        $rental->update(['status'=>'rejected']);

        return response()->json([
            'message'=>'Rental request rejected',
            'data'=>$rental
        ]);
    }

    // إلغاء الطلب (من المستأجر)
    public function cancel(Rental $rental)
    {
        if ($rental->renter_id !== auth()->id()) {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        if ($rental->status !== 'pending') {
            return response()->json(['message'=>'Cannot cancel after processing'],422);
        }

        $rental->update(['status'=>'cancelled']);

        return response()->json([
            'message'=>'Rental request cancelled',
            'data'=>$rental
        ]);
    }
    // المستأجر: طلباته الخاصة
    public function myRentals()
    {
        $rentals = Rental::with('car')
            ->where('renter_id', auth()->id())
            ->latest()
            ->get();

        return response()->json($rentals);
    }

// صاحب السيارة: الطلبات على سياراته
    public function ownerRentals()
    {
        $rentals = Rental::with('car')
            ->whereHas('car', function($q){
                $q->where('owner_id', auth()->id());
            })
            ->latest()
            ->get();

        return response()->json($rentals);
    }

// الأدمن: كل الطلبات
    public function allRentals()
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        $rentals = Rental::with(['car','renter'])->latest()->get();

        return response()->json($rentals);
    }

}

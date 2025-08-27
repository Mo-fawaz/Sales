<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Request;
use App\Http\Requests\Cars\TaxiRequestActionRequest;
use App\Http\Requests\Cars\TaxiRequestRequest;
use App\Models\Car;
use App\Models\TaxiRequest;

class TaxiRequestController extends Controller
{
    // تقديم طلب تكسي
    public function store(TaxiRequestRequest $request)
    {
        $validated = $request->validated();

        // البحث عن سيارات تكسي مناسبة
        $query = Car::where('status', 'approved')
            ->where('is_taxi', true);

        if (!empty($validated['car_type'])) {
            $query->where('type', $validated['car_type']);
        }

        // جلب السيارات المناسبة
        $cars = $query->get();

        // إذا ما في سيارات مناسبة → رفض الطلب مباشرة
        if ($cars->isEmpty()) {
            return TaxiRequest::create([
                'user_id'          => auth()->id(),
                'pickup_location'  => $validated['pickup_location'],
                'dropoff_location' => $validated['dropoff_location'] ?? null,
                'passengers'       => $validated['passengers'],
                'car_type'         => $validated['car_type'] ?? null,
                'status'           => 'rejected',
                'rejection_reason' => 'لا يوجد سيارات تكسي مناسبة حالياً',
            ]);
        }

        // حساب السعر المبدئي (مثلاً أرخص سعر بين السيارات المناسبة)
        $basePrice = $cars->min('price_per_km') ?? 0;

        return TaxiRequest::create([
            'user_id'          => auth()->id(),
            'pickup_location'  => $validated['pickup_location'],
            'dropoff_location' => $validated['dropoff_location'] ?? null,
            'passengers'       => $validated['passengers'],
            'car_type'         => $validated['car_type'] ?? null,
            'status'           => 'pending',
            'estimated_price'  => $basePrice, // 👈 السعر المبدئي
        ]);
    }

    // قبول الطلب (من طرف صاحب التكسي)
    public function accept(TaxiRequestActionRequest $request, TaxiRequest $taxiRequest)
    {
        if ($taxiRequest->status !== 'pending') {
            return response()->json(['message' => 'Request already processed'], 400);
        }

        $car = Car::where('id', $request->car_id)
            ->where('owner_id', auth()->id())
            ->where('is_taxi', true)
            ->first();

        if (!$car) {
            return response()->json(['message' => 'Unauthorized or invalid car'], 403);
        }

        $taxiRequest->update([
            'status' => 'accepted',
            'car_id' => $car->id
        ]);

        return response()->json(['message' => 'Request accepted', 'data' => $taxiRequest]);
    }

    // رفض الطلب
    public function reject(TaxiRequestActionRequest $request, TaxiRequest $taxiRequest)
    {
        if ($taxiRequest->status !== 'pending') {
            return response()->json(['message' => 'Request already processed'], 400);
        }

        $taxiRequest->update([
            'status' => 'rejected',
            'rejection_reason' => 'تم رفض الطلب من قبل السائق'
        ]);

        return response()->json(['message' => 'Request rejected', 'data' => $taxiRequest]);
    }

    // إلغاء الطلب (من طرف المستخدم)
    public function cancel(TaxiRequestActionRequest $request, TaxiRequest $taxiRequest)
    {
        if ($taxiRequest->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($taxiRequest->status !== 'pending') {
            return response()->json(['message' => 'Request cannot be cancelled'], 400);
        }

        $taxiRequest->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Request cancelled']);
    }
    public function index(Request $request)
    {
        $user = auth()->user();

        // validation بسيط
        $validated = $request->validate([
            'status' => 'nullable|in:pending,accepted,rejected,cancelled',
        ]);

        if (!$user->cars()->where('is_taxi', true)->exists()) {
            // مستخدم عادي → طلباته الخاصة
            $query = TaxiRequest::where('user_id', $user->id);
        } else {
            // صاحب تاكسي → الطلبات pending المطابقة لأنواع سياراته
            $carTypes = $user->cars()->where('is_taxi', true)->pluck('type');
            $query = TaxiRequest::where('status', 'pending')
                ->whereIn('car_type', $carTypes->toArray());
        }

        // فلترة حسب الحالة إذا موجودة
        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        return response()->json($query->latest()->with('car','user')->paginate(10));
    }

}




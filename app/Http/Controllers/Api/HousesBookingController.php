<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\HousesBookingResource;
use App\Models\Houses;
use App\Models\Houses_Booking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class HousesBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
            return HousesBookingResource::collection(Houses_Booking::get());

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validatedData = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'house_id' => 'required|exists:users,id',
        'start_date' => ['required', 'date', 'after_or_equal:' . Carbon::today()->toDateString()],
        'end_date' => 'required|date|after_or_equal:start_date',
        'total_price' => 'required|numeric',
        'status' => 'in:pending,confirmed,cancelled',
        
    ]);

    if ($validatedData->fails()) {
        return response()->json([
            'errors' => $validatedData->errors()
        ], 422);
    }

        $house = Houses::find($request->house_id);

    if (!$house) {
        return response()->json([
            'message' => 'House not found.'
        ], 404);
    }

    // تحقق من حالة البيت
    $activeBooking = Houses_Booking::where('house_id', $request->house_id)
    ->whereIn('status', ['confirmed', 'pending']) // الحجز قيد التنفيذ أو مؤكد
    ->first();

    if ($activeBooking) {
    return response()->json([
        'message' => 'This house is currently booked or pending and cannot be reserved.'
    ], 409);

    // تحقق إذا المستخدم نفسه حجز نفس البيت بنفس التواريخ
    $existingHouse = Houses_Booking::where('user_id', $request->user_id)
        ->where('house_id', $request->house_id)
        ->where('start_date', $request->start_date)
        ->where('end_date', $request->end_date)
        ->first();

    if ($existingHouse) {
        return response()->json([
            'message' => 'You already booked this house for these dates.'
        ], 409);
    }

    $house = Houses_Booking::create([
        'user_id' => $request->user_id,
        'house_id' => $request->house_id,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'total_price' => $request->total_price,
        'status' => $request->status,
       
    ]);

    return response()->json([
        'message' => 'تم إنشاء حجز البيت بنجاح',
        'house' => $house
    ], 201);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
            //

        $validatedData = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'house_id' => 'required|exists:houses,id',
            'start_date' => ['required', 'date', 'after_or_equal:' . Carbon::today()->toDateString()],
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_price' => 'required|numeric',
            'status' => 'in:pending,confirmed,cancelled',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'errors' => $validatedData->errors()
            ], 422);
        }

        // جلب الحجز الحالي
        $booking = Houses_Booking::find($id);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found.'
            ], 404);
        }

        // جلب بيانات البيت
        $house = Houses::find($request->house_id);

        if (!$house) {
            return response()->json([
                'message' => 'House not found.'
            ], 404);
        }

        // تحديث بيانات الحجز
        $booking->update([
            
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Booking updated successfully.',
            'booking' => $booking
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\HousesResource;
use App\Models\Houses;

use App\Models\HousesImages;
use Illuminate\Support\Facades\Validator;

class HousesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return HousesResource::collection(Houses::get());

    }

    /**
     * Show the form for creating a new resource.
     */
   
    public function create(Request $request)
{
    
    $validatedData = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'location' => 'required|string',
        'price_per_night' => 'required|numeric',
        'amenities' => 'required|json',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validatedData->fails()) {
        return response()->json([
            'errors' => $validatedData->errors()
        ], 422);
    }

    // تحويل الـ amenities لمصفوفة مرتبة
    $incomingAmenities = collect(json_decode($request->amenities, true))->sort()->values()->toArray();

    // البحث عن بيت مطابق
    $existingHouse = Houses::where('user_id', $request->user_id)
        ->where('title', $request->title)
        ->where('location', $request->location)
        ->where('description', $request->description)
        ->where('price_per_night', $request->price_per_night)
        ->get()
        ->filter(function ($house) use ($incomingAmenities) {
            $storedAmenities = collect(json_decode($house->amenities, true))->sort()->values()->toArray();
            return $storedAmenities == $incomingAmenities;
        })->first();

    if ($existingHouse) {
        return response()->json([
            'message' => 'هذا البيت موجود مسبقًا بنفس المواصفات'
        ], 409);
    }
    
    $house = Houses::create([
        'user_id' => $request->user_id,
        'title' => $request->title,
        'location' => $request->location,
        'description' => $request->description,
        'price_per_night' => $request->price_per_night,
        'amenities' => json_encode($incomingAmenities), // حفظها بشكل مرتب
    ]);
        if ($request->hasFile('image')) {
        $images = $request->file('image');

        // إذا كانت صورة وحدة، حولها لمصفوفة
        if (!is_array($images)) {
            $images = [$images];
        }

        foreach ($images as $img) {
            $path1 = $img->store('houses', 'public');
            HousesImages::create([
                'house_id' => $house->id,
                'path' => $path1
            ]);
        }
    } 

    return response()->json([
        'message' => 'تم إنشاء البيت بنجاح',
        'house' => $house
    ], 201);
}

    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $house = Houses::find($id);


        if (!$house) {
            return response()->json([
                'message' => 'لا يوجد هذا المنزل'
            ], 409);
        }


        return HousesResource::make(Houses::where("id",$id)->first());

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
        $house = Houses::find($id); 


        if ($house) {
            $data = [
                'user_id' => $request->user_id,
            'title' => $request->title,
            'location' => $request->location,
             'description' => $request->description,
            'price_per_night' => $request->price_per_night,
             'amenities' => $house->amenities, 
            ];


            $house->update($data);


            return response()->json([
                'message' => 'تمت تعديل المعلومات بنجاح'
            ], 205);
        } else {
            return response()->json([
                'message' => 'لا يوجد هذا البيت'
            ], 409);    
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $house= Houses::find($id);
        if(!$house)
        {
            return response()->json([
                'message' => 'لا يوجد هذا المنزل'
            ], 409);
        }
        $house->delete();

        return response()->json([
            'message' => 'تم حذف المنزل بنجاح'
        ], 200);
    }
}

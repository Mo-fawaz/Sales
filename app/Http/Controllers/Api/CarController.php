<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cars\CarRequest;
use App\Http\Requests\CarStoreRequest;
use App\Http\Requests\CarUpdateRequest;
use App\Models\Car;
use App\Models\CarImage;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    // عرض كل السيارات
    public function index()
    {
        $user = auth()->user();
        // الأدمن بيشوف كل السيارات
        if ($user->user_type === 'admin') {
            $cars = Car::with('images','owner')->paginate(10);
        } else {
            // المستخدم بيشوف بس approved
            $cars = Car::where('status','approved')->with('images','owner')->paginate(10);
        }

        return response()->json($cars);
    }

    // إضافة سيارة جديدة
    public function store(CarRequest $request)
    {
        $data = $request->validated();

        $car = Car::create(array_merge($data,[
            'owner_id'=>auth()->id(),
            'status'=>'pending'
        ]));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('cars','public');
                $car->images()->create(['path'=>$path]);
            }
        }

        return response()->json([
            'message'=>'Car submitted for approval',
            'data'=>$car->load('images')
        ]);
    }

    // عرض تفاصيل سيارة
    public function show(Car $car)
    {
        if ($car->status !== 'approved' && $car->owner_id !== auth()->id()) {
            return response()->json(['message'=>'Car not available'],403);
        }

        return response()->json($car->load('images','owner','reviews'));
    }

    // تعديل سيارة
    public function update(\App\Http\Requests\Cars\CarUpdateRequest $request, Car $car)
    {
        if ($car->owner_id !== auth()->id()) {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        $data = $request->validated();
        $car->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('cars','public');
                $car->images()->create(['path'=>$path]);
            }
        }

        return response()->json([
            'message'=>'Car updated',
            'data'=>$car->load('images')
        ]);
    }

    // حذف سيارة
    public function destroy(Car $car)
    {
        if ($car->owner_id !== auth()->id()) {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        // حذف صورها من التخزين
        foreach ($car->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        $car->delete();

        return response()->json(['message'=>'Car deleted']);
    }

    // حذف صورة محددة
    public function deleteImage(Car $car, CarImage $image)
    {
        if ($image->car_id !== $car->id) {
            return response()->json(['message'=>'Image not belongs to this car'],400);
        }

        if ($car->owner_id !== auth()->id()) {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json(['message'=>'Image deleted']);
    }

    // موافقة الأدمن على السيارة
    public function approve(Car $car)
    {
        $user = auth()->user();

        if ($user->user_type !== 'admin') {
            return response()->json(['message'=>'Only admin can approve cars'],403);
        }
        $car->status = 'approved';
        $car->save();

        return response()->json([
            'message'=>'Car approved',
            'data'=>$car->load('images','owner')
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HotelResource;
use App\Models\Hotel;
use App\Traits\GeneralTrait;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    use UploadTrait, GeneralTrait;


    public function index()
    {
        return $this->apiResponse(HotelResource::collection(Hotel::with(['images', 'city'])->get()));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'location' => 'required|string',
            'description' => 'nullable|string',
            'phone' => 'nullable|string',
            'city_id' => 'required|exists:cities,id',
            'stars' => 'nullable|integer|min:1|max:5',
            'images' => 'nullable|array',
            'images.*' => 'file|image|max:3072',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(null, false, $validator->errors(), 400);
        }
        $data = $validator->validated();
        $hotel = Hotel::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $this->verifyAndStoreImageForeach($image, 'Hotel', 'public', $hotel->id, Hotel::class);
            }
        }
        return $this->apiResponse(
            new HotelResource($hotel->load(['images', 'city'])),
            true,
            'Hotel created successfully',
            201
        );
    }





    public function show(string $id)
    {
        $hotel = Hotel::with(['images', 'city'])->find($id);

        if (!$hotel) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new HotelResource($hotel),
        ]);
    }


    public function update(Request $request, $id)
    {
        //dd($request->file());

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|nullable|string',
                'city_id' => 'sometimes|exists:cities,id',
                'stars' => 'sometimes|integer|min:1|max:5',
                'phone' => 'sometimes|nullable|string|max:20',
                'location' => 'sometimes|nullable|string|max:255',
                'images' => 'sometimes|array',
                'images.*' => 'file|image|max:3072',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse(null, false, $validator->errors(), 422);
            }

            $hotel = Hotel::find($id);
            if (!$hotel) {
                return $this->apiResponse(null, false, 'Hotel not found', 404);
            }

            $updateData = $request->only([
                'name',
                'description',
                'city_id',
                'stars',
                'phone',
                'location',
            ]);

            $hotel->update($updateData);

            if ($request->hasFile('images')) {
                foreach ($hotel->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage->filename);
                    $oldImage->delete();
                }
                foreach ($request->file('images') as $photo) {
                    $this->verifyAndStoreImageForeach($photo, 'Hotel', 'public', $id, Hotel::class);
                }
            }


            DB::commit();

            return $this->apiResponse(new HotelResource($hotel->fresh('images', 'city')));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(string $id)
    {
        try {
            $hotel = Hotel::findOrFail($id);

            foreach ($hotel->images as $image) {
                Storage::disk('public')->delete('Hotel/' . $image->filename);
                $image->delete();
            }
            $hotel->delete();
            return response()->json(['message' => 'hotel deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

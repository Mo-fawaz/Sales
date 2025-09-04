<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use App\Traits\GeneralTrait;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RestaurantController extends Controller
{
    use GeneralTrait, UploadTrait;

    public function index()
    {
        return $this->apiResponse(RestaurantResource::collection(Restaurant::with(['images', 'city'])->get()));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'location' => 'required|string',
            'description' => 'nullable|string',
            'phone' => 'nullable|string',
            'cuisine_type' => 'nullable|string',
            'opening_hours' => 'nullable|string',
            'city_id' => 'required|exists:cities,id',
            'images' => 'nullable|array',
            'images.*' => 'file|image|max:3072',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, false, $validator->errors(), 400);
        }

        $data = $validator->validated();
        $restaurant = Restaurant::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $this->verifyAndStoreImageForeach($image, 'Restaurant', 'public', $restaurant->id, Restaurant::class);
            }
        }

        return $this->apiResponse(
            new RestaurantResource($restaurant->load(['images', 'city'])),
            true,
            'Restaurant created successfully',
            201
        );
    }


    public function show(string $id)
    {
        $restaurant = Restaurant::with(['images', 'city'])->find($id);

        if (!$restaurant) {
            return $this->apiResponse(null, false, 'Restaurant not found', 404);
        }

        return $this->apiResponse(new RestaurantResource($restaurant));
    }

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string',
                'location' => 'sometimes|required|string',
                'description' => 'nullable|string',
                'phone' => 'nullable|string',
                'cuisine_type' => 'nullable|string',
                'opening_hours' => 'nullable|string',
                'city_id' => 'sometimes|exists:cities,id',
                'images' => 'nullable|array',
                'images.*' => 'file|image|max:3072',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse(null, false, $validator->errors(), 422);
            }

            $restaurant = Restaurant::find($id);
            if (!$restaurant) {
                return $this->apiResponse(null, false, 'Restaurant not found', 404);
            }

            $restaurant->update($validator->validated());

            if ($request->hasFile('images')) {
                foreach ($restaurant->images as $oldImage) {
                    Storage::disk('public')->delete("Restaurant/" . $oldImage->filename);
                    $oldImage->delete();
                }

                foreach ($request->file('images') as $photo) {
                    $this->verifyAndStoreImageForeach($photo, 'Restaurant', 'public', $id, Restaurant::class);
                }
            }

            DB::commit();

            return $this->apiResponse(
                new RestaurantResource($restaurant->fresh('images', 'city')),
                true,
                'Restaurant updated successfully',
                200
            );
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
            $product = Restaurant::findOrFail($id);

            foreach ($product->images as $image) {
                Storage::disk('public')->delete('Restaurant/' . $image->filename);
                $image->delete();
            }
            $product->delete();
            return response()->json(['message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

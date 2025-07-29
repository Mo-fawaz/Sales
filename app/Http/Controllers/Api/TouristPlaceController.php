<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TouristPlaceResource;
use App\Models\TouristPlace;
use App\Traits\GeneralTrait;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TouristPlaceController extends Controller
{
    use GeneralTrait, UploadTrait;

    public function index()
    {
        return $this->apiResponse(
            TouristPlaceResource::collection(
                TouristPlace::with(['images', 'city'])->get()
            )
        );
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string',
            'location'       => 'required|string',
            'description'    => 'nullable|string',
            'entry_fee'      => 'nullable|numeric',
            'opening_hours'  => 'nullable|string',
            'phone'          => 'nullable|string',
            'city_id'        => 'required|exists:cities,id',
            'images'         => 'nullable|array',
            'images.*'       => 'file|image|max:3072',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, false, $validator->errors(), 400);
        }

        $place = TouristPlace::create($validator->validated());

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $this->verifyAndStoreImageForeach($image, 'TouristPlaces', 'public', $place->id, TouristPlace::class);
            }
        }

        return $this->apiResponse(
            new TouristPlaceResource($place->load(['images', 'city'])),
            true,
            'Tourist Place created successfully',
            201
        );
    }


    public function show($id)
    {
        $place = TouristPlace::with(['images', 'city'])->find($id);

        if (!$place) {
            return $this->apiResponse(null, false, 'Tourist Place not found', 404);
        }

        return $this->apiResponse(new TouristPlaceResource($place));
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'sometimes|required|string',
            'location'       => 'sometimes|required|string',
            'description'    => 'nullable|string',
            'entry_fee'      => 'nullable|numeric',
            'opening_hours'  => 'nullable|string',
            'phone'          => 'nullable|string',
            'city_id'        => 'sometimes|exists:cities,id',
            'images'         => 'nullable|array',
            'images.*'       => 'file|image|max:3072',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, false, $validator->errors(), 400);
        }

        $place = TouristPlace::find($id);
        if (!$place) {
            return $this->apiResponse(null, false, 'Tourist Place not found', 404);
        }

        $place->update($validator->validated());

        if ($request->hasFile('images')) {
            foreach ($place->images as $oldImage) {
                Storage::disk('public')->delete($oldImage->filename);
                $oldImage->delete();
            }

            foreach ($request->file('images') as $image) {
                $this->verifyAndStoreImageForeach($image, 'TouristPlaces', 'public', $id, TouristPlace::class);
            }
        }

        return $this->apiResponse(
            new TouristPlaceResource($place->fresh(['images', 'city'])),
            true,
            'Tourist Place updated successfully',
            200
        );
    }
    public function destroy($id)
    {
        try {
            $tp = TouristPlace::findOrFail($id);

            foreach ($tp->images as $image) {
                Storage::disk('public')->delete('TouristPlaces/' . $image->filename);
                $image->delete();
            }
            $tp->delete();
            return response()->json(['message' => 'Tourist Place deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cars\CarsReviewRequest;
use App\Models\CarsReview;

class CarsReviewController extends Controller

{
    public function index($carId)
{
    $reviews = CarsReview::where('car_id', $carId)
        ->with('user:id,name')
        ->latest()
        ->paginate(10);

    return response()->json($reviews);
}
    public function store(CarsReviewRequest $request)
    {
        $review = CarsReview::create([
            'user_id' => auth()->id(),
            'car_id'  => $request->car_id,
            'rating'  => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json($review, 201);
    }
    public function update(CarsReviewRequest $request, CarsReview $review)
    {
        if ($review->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->update($request->only(['rating','comment']));

        return response()->json($review);
    }
    public function destroy(CarsReview $review)
    {
        if ($review->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully']);
    }





}

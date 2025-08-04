<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $v = Vehicle::all();
        return response()->json($v);
    }


    public function search(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'pickup_time' => 'required|date',
            'number_of_passengers' => 'required|integer|min:1',
            'self_drive' => 'nullable|boolean',
        ]);

        $vehicles = Vehicle::query()
            ->where('location', 'LIKE', '%' . $request->location . '%')
            ->where('available_from', '<=', $request->pickup_time)
            ->where('available_to', '>=', $request->pickup_time)
            ->where('seats', '>=', $request->number_of_passengers);

        if ($request->has('self_drive')) {
            $vehicles->where('self_drive', $request->self_drive);
        }

        $results = $vehicles->get();

        return response()->json([
            'count' => $vehicles->count(),
            'vehicles' => $results
        ]);
    }
}

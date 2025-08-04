<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    use GeneralTrait;
    protected function user()
    {
        return User::find(1);
    }

    public function index()
    {
        $favorites = $this->user()->favorites()->with('favoritable')->get();

        return response()->json([
            'data' => $favorites
        ]);
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'favoritable_type' => 'required|string',
            'favoritable_id' => 'required|integer',
        ]);

        $user = $this->user();

        $favorite = $user->favorites()
            ->where('favoritable_type', $request->favoritable_type)
            ->where('favoritable_id', $request->favoritable_id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'Removed from favorites']);
        } else {
            $user->favorites()->create([
                'favoritable_type' => $request->favoritable_type,
                'favoritable_id' => $request->favoritable_id,
            ]);
            return response()->json(['message' => 'Added to favorites']);
        }
    }
}

// Auth : 
//  public function index()
//     {
//         $favorites = Auth::user()->favorites()->with('favoritable')->get();

//         return response()->json([
//             'data' => $favorites
//         ]);
//     }

//     public function toggle(Request $request)
//     {
//         $request->validate([
//             'favoritable_type' => 'required|string',
//             'favoritable_id' => 'required|integer',
//         ]);

//         $user = Auth::user();

//         $favorite = $user->favorites()
//             ->where('favoritable_type', $request->favoritable_type)
//             ->where('favoritable_id', $request->favoritable_id)
//             ->first();

//         if ($favorite) {
//             $favorite->delete();
//             return response()->json(['message' => 'Removed from favorites']);
//         } else {
//             $user->favorites()->create([
//                 'favoritable_type' => $request->favoritable_type,
//                 'favoritable_id' => $request->favoritable_id,
//             ]);
//             return response()->json(['message' => 'Added to favorites']);
//         }
//     }

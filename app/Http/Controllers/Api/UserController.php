<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return UserResource::collection(User::get());
    }


    /**
     * Store a newly created resource in storage.
     */
    public function create(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'passport' => 'required|string',
            'password' => 'required|string',
            'nationality' => 'required|string',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'errors' => $validatedData->errors()
            ], 422);
        }


        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'passport' => $request->passport,
            'password' => $request->password,
            'nationality' => $request->nationality
        ]);


        return response()->json([
            'message' => 'تم إنشاء المستخدم بنجاح',
            'user' => $user
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $existingUser = User::where('id', $id)->get()->first();


        if (!$existingUser) {
            return response()->json([
                'message' => 'لا يوجد هذا المستخدم'
            ], 409);
        }


        return UserResource::make(User::where("id",$id)->first());
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id); 


        if ($user) {
            $data = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'passport' => $request->passport,
                'nationality' => $request->nationality
            ];


            if ($request->has('password') && $request->password) {
                $data['password'] = Hash::make($request->password);
            }


            $user->update($data);


            return response()->json([
                'message' => 'تمت تعديل المعلومات بنجاح'
            ], 205);
        } else {
            return response()->json([
                'message' => 'لا يوجد هذا المستخدم'
            ], 409);    
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);


        if (!$user) {
            return response()->json([
                'message' => 'لا يوجد هذا المستخدم'
            ], 409);
        }


        $user->delete();


        return response()->json([
            'message' => 'تم حذف المستخدم بنجاح'
        ], 200);
    }

}
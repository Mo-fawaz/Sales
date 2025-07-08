<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Mail\OtpMail;
use App\Mail\SendOtpMail;
use App\Models\User;
use Carbon\Carbon;
use Dotenv\Validator;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $otp = rand(100000, 999999);
            $user = User::create(
                [
                    'first_name' => $data['first_name'],
                    'last_name'  => $data['last_name'],
                    'email'      => $data['email'],
                    'phone'      => $data['phone'],
                    'password'   => Hash::make($data['password']),
                    'otp'             => $otp,
                    'otp_expires_at'  => Carbon::now()->addMinutes(10),
                ]);
            $token = $user->createToken('api_token')->plainTextToken;
            Mail::to($user->email)->send(new SendOtpMail($otp));
            DB::commit();
            return response()->json([
                'message' => 'User registered successfully.',
                'user'    => $user,
                'token'   => $token,
            ]);
        }catch (\Exception $e) {
            DB::rollBack();
            Log::error('Register Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred during registration.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function verifyOtp(Request $request)
    {
        try {
            $user = auth()->user();
            $request->validate([
                'otp'     => 'required',
            ]);

            if ($user->is_verified) {
                return response()->json(['message' => 'User already verified']);
            }

            if ($user->otp !== $request->otp) {
                return response()->json(['message' => 'Invalid OTP'], 400);
            }

            if (now()->greaterThan($user->otp_expires_at)) {
                return response()->json(['message' => 'OTP expired'], 400);
            }

            $user->update([
                'is_verified'     => true,
                'otp'             => null,
                'otp_expires_at'  => null,
            ]);

            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json([
                'message' => 'Account verified successfully',
                'token'   => $token,
                'user'    => $user,
            ]);

        } catch (\Exception $e) {
            Log::error('OTP Verification Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred during OTP verification.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    private function generateAndSendOtp(User $user)
    {
        $otp = rand(100000, 999999);
        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'change_password' => true
        ]);
        Mail::to($user->email)->send(new SendOtpMail($otp));
        $token = $user->createToken('api_token')->plainTextToken;
        return response()->json([
            'message' => 'OTP sent to your email.',
            'token' => $token,
        ]);
    }
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user->otp || !$user->otp_expires_at) {
                return $this->generateAndSendOtp($user);
            }
            if (now()->greaterThan($user->otp_expires_at)) {
                return $this->generateAndSendOtp($user);
            }
            $expiresIn = now()->diffInMinutes($user->otp_expires_at);
            return response()->json([
                'message' => 'OTP already sent and still valid.',
                'otp_expires_in_minutes' => $expiresIn
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send OTP.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'otp'     => 'required',
        ]);
        $user = auth()->user();
        if ($user->otp !== $request->otp) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }
        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP expired'], 400);
        }
        $request->validate([
            'otp'                  => 'required',
            'password' => 'required|string|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|confirmed',
            'password_confirmation' => 'required|string|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
        ]);
        $user = auth()->user();
        if ($user->otp !== $request->otp) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }
        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP expired'], 400);
        }
        $user->update([
            'password'        => Hash::make($request->new_password),
            'otp'             => null,
            'otp_expires_at'  => null,
            'change_password' => false,
        ]);
        return response()->json(['message' => 'Password reset successful']);
    }
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required',
            'password' => 'required',
        ]);

  
        $login = $request->login;
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::where($fieldType, $login)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $token = $user->createToken('api_token')->plainTextToken;
        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Auth;

class UserAuthController extends Controller
{
    /**
     * Register new user with robust error handling
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users',
                'phone' => 'required|string|unique:users|max:20',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 422,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()->all()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => 'success',
                'code' => 201,
                'message' => 'Registration successful',
                'data' => [
                    'user' => $user,
                    'token' => $user->createToken('user_token')->plainTextToken
                ]
            ], 201);
        } catch (ValidationException $e) {
            // This will catch validation exceptions specifically
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Registration failed. Please try again.',
                'system_error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * User login with comprehensive error handling
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'remember_me' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = $request->user();

        // Create token with expiration based on remember_me
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->accessToken;

        if ($request->remember_me) {
            // Extend token expiration (30 days)
            Passport::personalAccessTokensExpireIn(Carbon::now()->addDays(30));
        } else {
            // Default expiration (1 day)
            Passport::personalAccessTokensExpireIn(Carbon::now()->addDay());
        }

        return response()->json([
            'status' => 'success',
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'access_token' => $token,
            'user' => $user
        ]);
    }
    /**
     * Secure logout with token revocation
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Successfully logged out'
            ])->cookie('remember_token', '', -1);;
        } catch (Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Logout failed'
            ], 500);
        }
    }

    /**
     * Get authenticated user details
     */
    public function me(Request $request)
    {
        try {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $request->user()
            ]);
        } catch (Exception $e) {
            Log::error('User Details Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to fetch user details'
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class AuthController extends Controller
{
    use ThrottlesLogins;

    // Add this method - it's required by ThrottlesLogins trait
    public function username()
    {
        return 'email';
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Check for too many login attempts
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $seconds = $this->limiter()->availableIn(
                $this->throttleKey($request)
            );

            return response()->json([
                'message'            => 'Too many login attempts. Please try again in ' . $seconds . ' seconds.',
                'seconds_remaining'  => $seconds
            ], 429);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            $this->incrementLoginAttempts($request);
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken(
            'mobile_auth_token',
            ['*'],
            now()->addDays(7)
        )->plainTextToken;

        $this->clearLoginAttempts($request);

        return response()->json([
            'user'       => new UserResource($user),
            'token'      => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration') // 60 * 24 = 24 hours
        ]);

    }

    public function refresh(Request $request)
    {
        try {
            $user = $request->user();

            $currentToken = $user->currentAccessToken();
            if (!$currentToken) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $currentToken->delete();

            $token = $user->createToken('auth_token', ['*'], now()->addDays(7))->plainTextToken;

            return response()->json([
                'token'      => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed'
            ], 500);
        }
    }

    public function user(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                throw new \Exception('Unauthenticated');
            }

            return response()->json([
                'user' => new UserResource($user)
            ]);
        } catch (\Exception $e) {
            Log::error('Profile error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }



    // Google OAuth for React Native API
    public function redirectToGoogle()
    {
        try {
            $redirectUrl = Socialite::driver('google')
                ->redirect()
                ->getTargetUrl();

            return response()->json(['redirect_url' => $redirectUrl]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Authentication service unavailable'], 503);
        }
    }

    public function handleGoogle(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_token' => 'required|string',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            $client = new \Google_Client(['client_id' => config('services.google.client_id')]);
            $payload = $client->verifyIdToken($request->input('id_token'));
    
            if ($payload && ($payload['email_verified'] ?? false) === true) {
                $googleId = $payload['sub'];
                $email = strtolower($payload['email']);

                $user = User::where('google_id', $googleId)->first();
                if (!$user) {
                    $user = User::whereRaw('LOWER(email) = ?', [$email])->first();
                    if ($user) {
                        $user->update(['google_id' => $googleId, 'email_verified_at' => now()]);
                    } else {
                        $user = User::create([
                            'google_id' => $googleId,
                            'name' => $payload['name'] ?? $email,
                            'email' => $email,
                            'email_verified_at' => now(),
                            'password' => bcrypt(Str::random(32)),
                        ]);
                    }
                }

                $token = $user->createToken('google_auth_token', ['*'], now()->addDays(7))->plainTextToken;
    
                return response()->json([
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]);
            }
    
            return response()->json(['error' => 'Invalid Google ID token.'], 401);
        } catch (\Throwable $th) {
            Log::error('Google login error: ' . $th->getMessage());
            return response()->json(['error' => 'Authentication failed'], 401);
        }
    }

}
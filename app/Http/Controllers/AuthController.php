<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Auth;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;



class AuthController extends Controller
{
    /**
     * Handle user registration request.
     *
     * @param RegistrationRequest $request  The incoming validated registration request.
     * @return JsonResponse  JSON response containing the created user or an error message.
     *
     * @throws Exception If user creation fails.
     */
    public function register(RegistrationRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();
            $user = User::create($credentials);

            Auth::login($user);

            // Mail::to($user->email)->send(new WelcomeMail($user));

            return response()->json(
                $user,
                201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }


    /**
     * Handle user login request.
     *
     * Attempts to authenticate the user using the provided credentials. On success,
     * regenerates the session and returns the authenticated user. On failure,
     * returns an error response.
     *
     * @param LoginRequest $request  The validated login request.
     * @return JsonResponse  JSON response with the authenticated user, an authentication error, or a server error.
     *
     * @throws Exception If an unexpected error occurs during login.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();
    
            $user = User::where('email', $credentials['email'])->first();
    
            if (!$user) {
                return response()->json([
                    'message' => 'No account found with this email address'
                ], 401);
            }
            
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Incorrect password'
                ], 401);
            }
    
            $user = Auth::user();
            $request->session()->regenerate();
            return response()->json($user, 200);
        }
        catch (Exception $err) {
            return response()->json([
                'message' => $err->getMessage(),
            ], 500);
        }
    }


    /**
     * Log out the authenticated user.
     *
     * Invalidates the current session and regenerates the CSRF token to prevent session fixation attacks.
     *
     * @param  Request  $request  The incoming HTTP request.
     * @return JsonResponse  JSON response confirming logout.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(
            ['message' => 'Logged out successfully']
        );
    }


    /**
     * Reset the authenticated user's password.
     *
     * This method validates the current password, ensures the new password meets complexity
     * requirements, and that it matches the confirmation. If successful, the password is updated.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $user = Auth::user();

            if (!Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect.',
                ], 401);
            }

            $user->password = Hash::make($validated['new_password']);
            $user->save();

            return response()->json([
                'message' => 'Password updated successfully.',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating password.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}

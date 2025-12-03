<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        $nameParts = explode(' ', $googleUser->getName(), 2);

        if (!$user) {
            $user = User::create([
                'name'      => $nameParts[0],
                'surname'   => $nameParts[1] ?? '',
                'email'     => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
                'birth_date' => '1970-01-01', 
                'address'   => 'unknown', 
                'password'  => bcrypt(uniqid('google_')),
            ]);
        } else {
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        $frontendUrl = env('APP_FRONTEND_URL') . '/';
        return redirect($frontendUrl);
    }
}
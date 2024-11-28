<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// use Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;



class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
        // return Socialite::driver('google')->redirect();

    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        // $googleUser = Socialite::driver('google')->user();
        $useremail = User::where('email', $googleUser->getEmail())->first();

        if (!$useremail) {
            return response()->json(['error' => 'Email not registered'], 403);
        }

        $user = User::where('google_id', $googleUser->id)->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'password' => null, 
            ]);
        }

        $googleId = $googleUser->getId();
            if (empty($googleId)) {
                return response()->json(['error' => 'Google ID not retrieved'], 500);
            }

        $user->update([
            'google_id' => $googleUser->getId(),
            'name' => $googleUser->getName(),
            
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }
}

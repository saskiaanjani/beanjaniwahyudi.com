<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function resetPassword(Request $request)
{
   
    $request->validate([
        'email' => 'required|email',
        'old_password' => 'required',
        'new_password' => 'required|min:6|confirmed',
    ]);

    $user = User::where('email', $request->email)->first();
    if (!$user) {
        return response()->json(['message' => 'Email tidak terdaftar!'], 404);
    }

    if (!Auth::attempt(['email' => $request->email, 'password' => $request->old_password])) {
        return response()->json(['message' => 'Password lama tidak cocok!'], 400);
    }

    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json(['message' => 'Password berhasil direset!']);
}
}

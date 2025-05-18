<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Pendaftaran berhasil',
            'token' => $user->createToken('API Token')->plainTextToken,
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Tidak diizinkan. Email atau kata sandi tidak valid.'
            ], 401);
        }
    
        return response()->json([
            'message' => 'Login berhasil',
            'token' => $user->createToken('API Token')->plainTextToken,
            'user' => $user
        ], 200);
    }
    
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Berhasil keluar log out'
        ]);
    }
    
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }
}

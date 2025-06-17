<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\RevokeLog;
use Carbon\Carbon;

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

        if ($user->status === 'revoked') {
            return response()->json([
                'message' => 'Akun anda telah di-revoke. Silahkan hubungi administrator.'
            ], 403);
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

    /**
     * Get all users with pagination
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUsers(Request $request)
    {
        $perPage = $request->input('per_page', 15); // Default 15 items per page
        $users = User::paginate($perPage);
        
        return response()->json([
            'users' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem()
            ]
        ]);
    }

    /**
     * Revoke a user's access
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeUser($id)
    {
        $user = User::find($id);
        $revoker = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        if ($user->status === 'revoked') {
            return response()->json([
                'message' => 'User sudah dalam status revoked'
            ], 400);
        }

        // Check cooldown
        $lastRevoke = RevokeLog::where('revoker_id', $revoker->id)
            ->where('revoked_at', '>=', Carbon::now()->subHours(24))
            ->first();

        if ($lastRevoke) {
            $nextRevokeTime = Carbon::parse($lastRevoke->revoked_at)->addHours(24);
            return response()->json([
                'message' => 'Anda harus menunggu 24 jam sebelum dapat melakukan revoke user lain',
                'next_revoke_time' => $nextRevokeTime->format('Y-m-d H:i:s')
            ], 429);
        }

        $user->status = 'revoked';
        $user->save();

        // Create revoke log
        RevokeLog::create([
            'revoker_id' => $revoker->id,
            'revoked_user_id' => $user->id,
            'revoked_at' => Carbon::now()
        ]);

        // Revoke all tokens
        $user->tokens()->delete();

        return response()->json([
            'message' => 'User berhasil di-revoke'
        ], 200);
    }

    /**
     * Reactivate a revoked user
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reactivateUser($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        if ($user->status === 'active') {
            return response()->json([
                'message' => 'User sudah dalam status aktif'
            ], 400);
        }

        $user->status = 'active';
        $user->save();

        return response()->json([
            'message' => 'User berhasil diaktifkan kembali'
        ], 200);
    }
}

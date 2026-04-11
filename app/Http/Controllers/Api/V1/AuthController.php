<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        // Support login by email or username
        $field = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user  = User::where($field, $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email/username atau password salah.'],
            ]);
        }

        if (! $user->isDaerah()) {
            return response()->json(['message' => 'Akun ini bukan akun daerah.'], 403);
        }

        if (! $user->daerah?->is_active) {
            return response()->json(['message' => 'Akun daerah tidak aktif.'], 403);
        }

        $token = $user->createToken('daerah-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'username' => $user->username,
                'daerah'   => $user->daerah,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil.']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('daerah');
        return response()->json([
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'username' => $user->username,
            'role'     => $user->role,
            'daerah'   => $user->daerah,
        ]);
    }
}

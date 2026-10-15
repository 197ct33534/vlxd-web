<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthTokenController extends Controller
{
    /**
     * Issue Sanctum token for driver / admin mobile clients (Phase 3 API).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:100',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Sai email hoặc mật khẩu.'], 422);
        }

        if (!$user->isAdmin() && !$user->isDriver()) {
            return response()->json(['message' => 'Tài khoản không được dùng API giao hàng.'], 403);
        }

        $token = $user->createToken($data['device_name'] ?? 'driver-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    public function destroy(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Đã thu hồi token.']);
    }
}

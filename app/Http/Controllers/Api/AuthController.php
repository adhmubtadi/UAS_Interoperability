<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validasi input
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'in:admin,customer'
        ]);

        try {
            // Buat user baru
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role ?? 'customer',
                'api_token' => Str::random(80)
            ]);

            return ResponseHelper::success([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token' => $user->api_token
            ], 'Registrasi berhasil', 201);

        } catch (\Exception $e) {
            return ResponseHelper::error('Registrasi gagal: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Login user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validasi input
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        try {
            // Cari user berdasarkan email
            $user = User::where('email', $request->email)->first();

            // Cek apakah user ada dan password benar
            if (!$user || !Hash::check($request->password, $user->password)) {
                return ResponseHelper::error('Email atau password salah', null, 401);
            }

            // Generate token baru
            $user->api_token = Str::random(80);
            $user->save();

            return ResponseHelper::success([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token' => $user->api_token
            ], 'Login berhasil', 200);

        } catch (\Exception $e) {
            return ResponseHelper::error('Login gagal: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Logout user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Ambil user yang sedang login dari request
            $user = $request->user();

            if (!$user) {
                return ResponseHelper::error('User tidak ditemukan', null, 401);
            }

            // Hapus token
            $user->api_token = null;
            $user->save();

            return ResponseHelper::success(null, 'Logout berhasil', 200);

        } catch (\Exception $e) {
            return ResponseHelper::error('Logout gagal: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Get authenticated user profile
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return ResponseHelper::error('User tidak ditemukan', null, 401);
            }

            return ResponseHelper::success([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ], 'Data user berhasil diambil', 200);

        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal mengambil data user: ' . $e->getMessage(), null, 500);
        }
    }
}

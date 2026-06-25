<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ApiUserController extends Controller
{
    /**
     * ========================================
     * REGISTER USER BARU
     * ========================================
     * Endpoint: POST /api/register
     * Body (JSON atau form-data):
     *   - name (required) - Username
     *   - full_name (optional) - Nama lengkap
     *   - email (required, unique)
     *   - password (required, min 8 char)
     *   - password_confirmation (required, sama dengan password)
     *   - role selalu customer dari API publik
     *   - phone_number (optional)
     */
    public function register(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:users,name',
                'full_name' => 'nullable|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed', // harus ada password_confirmation
                'role' => 'nullable|in:customer',
                'phone_number' => 'nullable|string|max:20',
            ]);

            // Jika validasi gagal
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Buat user baru
            $user = User::create([
                'name' => $request->name,
                'full_name' => $request->full_name ?? $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'customer',
                'account_status' => 'active',
                'phone_number' => $request->phone_number,
            ]);

            // Generate token untuk user (pakai Laravel Sanctum)
            $token = $user->createToken('auth_token')->plainTextToken;
            
            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'full_name' => $user->full_name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'phone_number' => $user->phone_number,
                        'profile_pic' => $user->profile_pic,
                    ],
                    'token' => $token,
                ]
            ], 201); // 201 = Created

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * LOGIN USER
     * ========================================
     * Endpoint: POST /api/login
     * Body (JSON atau form-data):
     *   - email (required)
     *   - password (required)
     */
    public function login(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek kredensial
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah'
                ], 401); // 401 = Unauthorized
            }

            // Ambil user yang login
            $user = Auth::user();

            if ($user->role !== 'admin' && ($user->account_status ?? 'active') !== 'active') {
                Auth::logout();

                return response()->json([
                    'success' => false,
                    'message' => 'Akun sedang dinonaktifkan. Silakan hubungi admin Motifnesia.'
                ], 403);
            }

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'full_name' => $user->full_name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'phone_number' => $user->phone_number,
                        'profile_pic' => $user->profile_pic,
                        'address_line' => $user->address_line,
                        'city' => $user->city,
                        'province' => $user->province,
                        'postal_code' => $user->postal_code,
                    ],
                    'token' => $token,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * GET PROFILE USER (butuh login)
     * ========================================
     * Endpoint: GET /api/profile?user_id={id}
     * Query params:
     *   - user_id (required for testing, optional when using Sanctum)
     * Headers: Authorization: Bearer {token}
     * 
     * Note: Untuk sekarang tanpa auth dulu, nanti tambahin middleware auth:sanctum
     */
    public function profile(Request $request)
    {
        try {
            // Use authenticated user from Sanctum
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User belum login'
                ], 401);
            }

            // Load addresses relationship
            $user->load('addresses');

            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil diambil',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'phone_number' => $user->phone_number,
                    'birth_date' => $user->birth_date,
                    'gender' => $user->gender,
                    'profile_pic' => $user->profile_pic,
                    
                    // DEPRECATED: Use 'addresses' instead
                    'address_line' => $user->address_line,
                    'city' => $user->city,
                    'province' => $user->province,
                    'postal_code' => $user->postal_code,
                    
                    // NEW: Multiple addresses support
                    'addresses' => $user->addresses,
                    'primary_address' => $user->addresses->where('is_primary', true)->first(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * UPDATE PROFILE USER (butuh login)
     * ========================================
     * Endpoint: PUT/PATCH /api/profile
     * Headers: Authorization: Bearer {token}
     * Body (semua optional):
     *   - full_name
     *   - phone_number
     *   - birth_date (format: YYYY-MM-DD)
     *   - gender (male/female)
     *   - address_line
     *   - city
     *   - province
     *   - postal_code
     *   - profile_pic (file gambar)
     */
    public function updateProfile(Request $request)
    {
        try {
            // Use authenticated user from Sanctum
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User belum login'
                ], 401);
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'user_id' => 'nullable|integer',
                'full_name' => 'nullable|string|max:255',
                'phone_number' => 'nullable|string|max:20',
                'birth_date' => 'nullable|date',
                'gender' => 'nullable|in:male,female,Laki-laki,Perempuan',
                'address_line' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'province' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:10',
                'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle upload profile pic (jika ada)
            if ($request->hasFile('profile_pic')) {
                // Hapus gambar lama jika ada
                if ($user->profile_pic && file_exists(public_path($user->profile_pic))) {
                    unlink(public_path($user->profile_pic));
                }

                // Upload gambar baru
                $image = $request->file('profile_pic');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/profiles'), $imageName);
                $user->profile_pic = 'images/profiles/' . $imageName;
            }

            // Update field yang ada di request
            if ($request->has('full_name')) {
                $user->full_name = $request->full_name;
            }
            if ($request->has('phone_number')) {
                $user->phone_number = $request->phone_number;
            }
            if ($request->has('birth_date')) {
                $user->birth_date = $request->birth_date;
            }
            if ($request->has('gender')) {
                $user->gender = $request->gender;
            }
            if ($request->has('address_line')) {
                $user->address_line = $request->address_line;
            }
            if ($request->has('city')) {
                $user->city = $request->city;
            }
            if ($request->has('province')) {
                $user->province = $request->province;
            }
            if ($request->has('postal_code')) {
                $user->postal_code = $request->postal_code;
            }

            // Save perubahan
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil diupdate',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'phone_number' => $user->phone_number,
                    'birth_date' => $user->birth_date,
                    'gender' => $user->gender,
                    'profile_pic' => $user->profile_pic,
                    'address_line' => $user->address_line,
                    'city' => $user->city,
                    'province' => $user->province,
                    'postal_code' => $user->postal_code,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // public function updateProfile(Request $request)
    // {
    //     try {
    //         // Ambil user yang sedang login
    //         if (!Auth::check()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'User belum login'
    //             ], 401);
    //         }

    //         $user = Auth::user();

    //         // Validasi input
    //         $validator = Validator::make($request->all(), [
    //             'full_name' => 'nullable|string|max:255',
    //             'phone_number' => 'nullable|string|max:20',
    //             'birth_date' => 'nullable|date',
    //             'gender' => 'nullable|in:male,female',
    //             'address_line' => 'nullable|string',
    //             'city' => 'nullable|string|max:100',
    //             'province' => 'nullable|string|max:100',
    //             'postal_code' => 'nullable|string|max:10',
    //             'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Validasi gagal',
    //                 'errors' => $validator->errors()
    //             ], 422);
    //         }

    //         // Handle upload profile pic (jika ada)
    //         if ($request->hasFile('profile_pic')) {
    //             // Hapus gambar lama jika ada
    //             if ($user->profile_pic && file_exists(public_path($user->profile_pic))) {
    //                 unlink(public_path($user->profile_pic));
    //             }

    //             // Upload gambar baru
    //             $image = $request->file('profile_pic');
    //             $imageName = time() . '_' . $image->getClientOriginalName();
    //             $image->move(public_path('images/profiles'), $imageName);
    //             $user->profile_pic = 'images/profiles/' . $imageName;
    //         }

    //         // Update field yang ada di request
    //         if ($request->has('full_name')) {
    //             $user->full_name = $request->full_name;
    //         }
    //         if ($request->has('phone_number')) {
    //             $user->phone_number = $request->phone_number;
    //         }
    //         if ($request->has('birth_date')) {
    //             $user->birth_date = $request->birth_date;
    //         }
    //         if ($request->has('gender')) {
    //             $user->gender = $request->gender;
    //         }
    //         if ($request->has('address_line')) {
    //             $user->address_line = $request->address_line;
    //         }
    //         if ($request->has('city')) {
    //             $user->city = $request->city;
    //         }
    //         if ($request->has('province')) {
    //             $user->province = $request->province;
    //         }
    //         if ($request->has('postal_code')) {
    //             $user->postal_code = $request->postal_code;
    //         }

    //         // Save perubahan
    //         $user->save();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Profile berhasil diupdate',
    //             'data' => [
    //                 'id' => $user->id,
    //                 'name' => $user->name,
    //                 'full_name' => $user->full_name,
    //                 'email' => $user->email,
    //                 'role' => $user->role,
    //                 'phone_number' => $user->phone_number,
    //                 'birth_date' => $user->birth_date,
    //                 'gender' => $user->gender,
    //                 'profile_pic' => $user->profile_pic,
    //                 'address_line' => $user->address_line,
    //                 'city' => $user->city,
    //                 'province' => $user->province,
    //                 'postal_code' => $user->postal_code,
    //             ]
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal mengupdate profile',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    

    /**
     * ========================================
     * LOGOUT USER
     * ========================================
     * Endpoint: POST /api/logout
     * Headers: Authorization: Bearer {token}
     */
    public function logout(Request $request)
    {
        try {
            // Revoke current user's token
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * GET ALL USERS (untuk admin)
     * ========================================
     * Endpoint: GET /api/users
     * Query params: 
     *   - per_page (default: 10)
     *   - role (filter by role: customer/admin)
     */
    public function index(Request $request)
    {
        try {
            // Check admin authorization
            if ($request->user()?->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden'
                ], 403);
            }

            // Ambil query params
            $perPage = $request->get('per_page', 10);
            $role = $request->get('role');

            // Query builder
            $query = User::query();

            // Filter by role (jika ada)
            if ($role) {
                $query->where('role', $role);
            }

            // Order by terbaru & paginate
            $users = $query->orderBy('id', 'desc')->paginate($perPage);

            // Hide password dari response
            $usersData = $users->items();
            foreach ($usersData as $user) {
                unset($user->password);
                unset($user->remember_token);
            }

            return response()->json([
                'success' => true,
                'message' => 'Users berhasil diambil',
                'data' => $usersData,
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * GET USER BY ID (untuk admin)
     * ========================================
     * Endpoint: GET /api/users/{id}
     */
    public function show(Request $request, $id)
    {
        try {
            // Check admin authorization
            if ($request->user()?->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden'
                ], 403);
            }

            $user = User::with('addresses')->find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diambil',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'phone_number' => $user->phone_number,
                    'birth_date' => $user->birth_date,
                    'gender' => $user->gender,
                    'profile_pic' => $user->profile_pic,
                    
                    // DEPRECATED: Use 'addresses' instead
                    'address_line' => $user->address_line,
                    'city' => $user->city,
                    'province' => $user->province,
                    'postal_code' => $user->postal_code,
                    
                    // NEW: Multiple addresses support
                    'addresses' => $user->addresses,
                    'primary_address' => $user->addresses->where('is_primary', true)->first(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * DELETE USER (untuk admin)
     * ========================================
     * Endpoint: DELETE /api/users/{id}
     */
    public function destroy(Request $request, $id)
    {
        try {
            // Check admin authorization
            if ($request->user()?->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden'
                ], 403);
            }

            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $user->update([
                'account_status' => 'suspended',
                'account_status_reason' => 'Dinonaktifkan melalui API admin.',
                'account_status_changed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dinonaktifkan'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

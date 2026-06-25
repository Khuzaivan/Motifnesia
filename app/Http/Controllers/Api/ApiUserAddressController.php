<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiUserAddressController extends Controller
{
    /**
     * ========================================
     * GET ALL USER ADDRESSES
     * ========================================
     * Endpoint: GET /api/addresses?user_id={id}
     */
    public function index(Request $request)
    {
        try {
            $userId = $request->user()->id;

            // Ambil semua alamat user, primary di atas
            $addresses = UserAddress::where('user_id', $userId)
                ->orderBy('is_primary', 'desc')
                ->orderBy('id', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Addresses berhasil diambil',
                'data' => [
                    'addresses' => $addresses,
                    'total' => $addresses->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil addresses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * ADD NEW ADDRESS
     * ========================================
     * Endpoint: POST /api/addresses
     * Body:
     *   - user_id (required)
     *   - label (optional) - Rumah, Kantor, Kost, dll
     *   - recipient_name (required)
     *   - phone_number (required)
     *   - address_line (required)
     *   - city (required)
     *   - province (required)
     *   - postal_code (required)
     *   - notes (optional)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'label' => 'nullable|string|max:255',
                'recipient_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'address_line' => 'required|string',
                'city' => 'required|string|max:100',
                'province' => 'required|string|max:100',
                'postal_code' => 'required|string|max:10',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek apakah ini alamat pertama user
            $isFirstAddress = UserAddress::where('user_id', $request->user()->id)->count() == 0;

            $address = UserAddress::create([
                'user_id' => $request->user()->id,
                'label' => $request->label,
                'recipient_name' => $request->recipient_name,
                'phone_number' => $request->phone_number,
                'address_line' => $request->address_line,
                'city' => $request->city,
                'province' => $request->province,
                'postal_code' => $request->postal_code,
                'notes' => $request->notes,
                'is_primary' => $isFirstAddress, // Alamat pertama otomatis jadi primary
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil ditambahkan',
                'data' => $address
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan alamat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * GET SINGLE ADDRESS
     * ========================================
     * Endpoint: GET /api/addresses/{id}?user_id={id}
     */
    public function show(Request $request, $id)
    {
        try {
            $userId = $request->user()->id;

            $address = UserAddress::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil diambil',
                'data' => $address
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil alamat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * UPDATE ADDRESS
     * ========================================
     * Endpoint: PUT/PATCH /api/addresses/{id}
     * Body: sama seperti store (semua optional kecuali user_id)
     */
    public function update(Request $request, $id)
    {
        try {
            $userId = $request->user()->id;

            $address = UserAddress::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'label' => 'nullable|string|max:255',
                'recipient_name' => 'nullable|string|max:255',
                'phone_number' => 'nullable|string|max:20',
                'address_line' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'province' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:10',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update hanya field yang dikirim
            $updateData = [];
            if ($request->has('label')) $updateData['label'] = $request->label;
            if ($request->has('recipient_name')) $updateData['recipient_name'] = $request->recipient_name;
            if ($request->has('phone_number')) $updateData['phone_number'] = $request->phone_number;
            if ($request->has('address_line')) $updateData['address_line'] = $request->address_line;
            if ($request->has('city')) $updateData['city'] = $request->city;
            if ($request->has('province')) $updateData['province'] = $request->province;
            if ($request->has('postal_code')) $updateData['postal_code'] = $request->postal_code;
            if ($request->has('notes')) $updateData['notes'] = $request->notes;

            $address->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil diupdate',
                'data' => $address->fresh()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate alamat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * SET ADDRESS AS PRIMARY
     * ========================================
     * Endpoint: POST /api/addresses/{id}/set-primary
     * Body: { "user_id": 1 }
     */
    public function setPrimary(Request $request, $id)
    {
        try {
            $userId = $request->user()->id;

            $address = UserAddress::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            // Set semua alamat user jadi non-primary
            UserAddress::where('user_id', $userId)->update(['is_primary' => false]);

            // Set alamat ini jadi primary
            $address->update(['is_primary' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Alamat utama berhasil diubah'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah alamat utama',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * DELETE ADDRESS
     * ========================================
     * Endpoint: DELETE /api/addresses/{id}?user_id={id}
     */
    public function destroy(Request $request, $id)
    {
        try {
            $userId = $request->user()->id;

            $address = UserAddress::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            // Jika alamat yang dihapus adalah primary, set alamat lain jadi primary
            $wasPrimary = $address->is_primary;
            $address->delete();

            if ($wasPrimary) {
                // Set alamat pertama yang tersisa jadi primary
                $nextAddress = UserAddress::where('user_id', $userId)->first();
                if ($nextAddress) {
                    $nextAddress->update(['is_primary' => true]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus alamat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * GET PRIMARY ADDRESS
     * ========================================
     * Endpoint: GET /api/addresses/primary?user_id={id}
     */
    public function getPrimary(Request $request)
    {
        try {
            $userId = $request->user()->id;

            $address = UserAddress::where('user_id', $userId)
                ->where('is_primary', true)
                ->first();

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum ada alamat utama',
                    'data' => null
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Alamat utama berhasil diambil',
                'data' => $address
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil alamat utama',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

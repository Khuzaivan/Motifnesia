<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserAddressController extends Controller
{
    /**
     * Store alamat baru
     */
    public function store(Request $request)
    {
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
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek apakah ini alamat pertama user
        $isFirstAddress = UserAddress::where('user_id', Auth::id())->count() == 0;

        $address = UserAddress::create([
            'user_id' => Auth::id(),
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
            'address' => $address
        ]);
    }

    /**
     * Update alamat
     */
    public function update(Request $request, $id)
    {
        $address = UserAddress::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan'
            ], 404);
        }

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
                'errors' => $validator->errors()
            ], 422);
        }

        $address->update([
            'label' => $request->label,
            'recipient_name' => $request->recipient_name,
            'phone_number' => $request->phone_number,
            'address_line' => $request->address_line,
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil diupdate',
            'address' => $address
        ]);
    }

    /**
     * Set alamat sebagai primary
     */
    public function setPrimary($id)
    {
        $address = UserAddress::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan'
            ], 404);
        }

        // Set semua alamat user jadi non-primary
        UserAddress::where('user_id', Auth::id())->update(['is_primary' => false]);

        // Set alamat ini jadi primary
        $address->update(['is_primary' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Alamat utama berhasil diubah'
        ]);
    }

    /**
     * Delete alamat
     */
    public function destroy($id)
    {
        $address = UserAddress::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan'
            ], 404);
        }

        // Jika alamat yang dihapus adalah primary, set alamat lain jadi primary
        if ($address->is_primary) {
            $address->delete();
            
            // Set alamat pertama yang tersisa jadi primary
            $nextAddress = UserAddress::where('user_id', Auth::id())->first();
            if ($nextAddress) {
                $nextAddress->update(['is_primary' => true]);
            }
        } else {
            $address->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil dihapus'
        ]);
    }
}

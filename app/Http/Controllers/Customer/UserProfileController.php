<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\PurchaseHistoryController;
use App\Models\User;

class UserProfileController extends \App\Http\Controllers\Controller
{
    /**
     * Tampilkan halaman Profil Pengguna.
     */
    public function index()
    {
        $user = Auth::user();

        $userProfile = [
            'username' => $user->name ?? 'guest',
            'profile_pic' => $user->profile_pic ?? 'placeholder_user.jpg',
            'profile_pic_url' => $user->profile_pic_url,
            'full_name' => $user->full_name ?? $user->name ?? 'Guest User',
            'birth_date' => $user->birth_date ?? null,
            'gender' => $user->gender ?? null,
            'email' => $user->email ?? null,
            'phone_number' => $user->phone_number ?? null,
            'address_line' => $user->address_line ?? null,
            'city' => $user->city ?? null,
            'province' => $user->province ?? null,
            'postal_code' => $user->postal_code ?? null,
        ];

        // Load user addresses
        $addresses = $user->addresses()->orderBy('is_primary', 'desc')->orderBy('id', 'asc')->get();

        $purchaseHistory = PurchaseHistoryController::getHistoryData();
        return view('customer.pages.userProfile', compact('userProfile', 'purchaseHistory', 'addresses'));
    }

    public function edit()
    {
        $user = Auth::user();

        $userProfile = [
            'username' => $user->name ?? 'guest',
            'profile_pic' => $user->profile_pic ?? 'placeholder_user.jpg',
            'profile_pic_url' => $user->profile_pic_url,
            'full_name' => $user->full_name ?? $user->name ?? 'Guest User',
            'birth_date' => $user->birth_date ?? null,
            'gender' => $user->gender ?? null,
            'email' => $user->email ?? null,
            'phone_number' => $user->phone_number ?? null,
            'address_line' => $user->address_line ?? null,
            'city' => $user->city ?? null,
            'province' => $user->province ?? null,
            'postal_code' => $user->postal_code ?? null,
        ];

        // Load user addresses
        $addresses = $user->addresses()->orderBy('is_primary', 'desc')->orderBy('id', 'asc')->get();
        $addressFormData = $addresses->mapWithKeys(function ($address) {
            return [$address->id => [
                'label' => $address->label,
                'recipient_name' => $address->recipient_name,
                'phone_number' => $address->phone_number,
                'address_line' => $address->address_line,
                'city' => $address->city,
                'province' => $address->province,
                'postal_code' => $address->postal_code,
                'notes' => $address->notes,
            ]];
        });

        return view('customer.pages.editProfile', compact('userProfile', 'addresses', 'addressFormData'));
    }

    /**
     * Update profile data (including optional profile photo upload)
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('auth.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $data = $request->validate([
            'username' => 'required|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:1',
            'profile_photo' => 'nullable|image|max:10240',
            // address fields
            'address_line' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
        ]);

        // Update basic fields
        // 'name' remains the username; 'full_name' stores the human-readable full name
        $user->name = $data['username'];
        if (isset($data['full_name'])) {
            $user->full_name = $data['full_name'];
        }
        $user->phone_number = $data['phone_number'] ?? $user->phone_number;
        $user->birth_date = $data['birth_date'] ?? $user->birth_date;
        $user->gender = $data['gender'] ?? $user->gender;

        // address
        $user->address_line = $data['address_line'] ?? $user->address_line;
        $user->city = $data['city'] ?? $user->city;
        $user->province = $data['province'] ?? $user->province;
        $user->postal_code = $data['postal_code'] ?? $user->postal_code;

        // Handle profile photo upload if ada
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            // Simpan langsung ke public/images agar bisa diakses via asset('images/...')
            $file->move(public_path('images'), $filename);
            $user->profile_pic = $filename;
        }

        $user->save();

        return redirect()->route('customer.profile.index')->with('success', 'Profil berhasil diperbarui.');
    }

    
}

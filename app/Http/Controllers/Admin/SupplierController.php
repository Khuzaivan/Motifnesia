<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    public function index()
    {
        Gate::authorize('is-owner');

        $suppliers = Supplier::withCount('procurements')
            ->with('user')
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->paginate(10);

        return view('admin.pages.suppliers.index', [
            'suppliers' => $suppliers,
            'activePage' => 'suppliers',
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('is-owner');

        $data = $this->validatedSupplier($request);
        $password = $data['password'] ?? null;
        unset($data['password']);

        DB::transaction(function () use ($data, $password) {
            $data['user_id'] = $this->syncSupplierUser(null, $data, $password);

            Supplier::create($data);
        });

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        Gate::authorize('is-owner');

        $data = $this->validatedSupplier($request, $supplier);
        $password = $data['password'] ?? null;
        unset($data['password']);

        DB::transaction(function () use ($supplier, $data, $password) {
            $data['user_id'] = $this->syncSupplierUser($supplier, $data, $password);
            $supplier->update($data);
        });

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        Gate::authorize('is-owner');

        $supplier->update(['status' => 'inactive']);

        if ($supplier->user) {
            $supplier->user->update([
                'account_status' => 'suspended',
                'account_status_reason' => 'Supplier dinonaktifkan oleh owner.',
                'account_status_changed_at' => now(),
                'account_status_changed_by' => auth()->id(),
            ]);
        }

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil dinonaktifkan.');
    }

    private function validatedSupplier(Request $request, ?Supplier $supplier = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:40',
            'address' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string|max:1000',
            'password' => ($supplier && $supplier->user_id) ? 'nullable|string|min:6' : 'nullable|string|min:6',
        ]);
    }

    private function syncSupplierUser(?Supplier $supplier, array $data, ?string $password): ?int
    {
        $email = $data['email'] ?? null;
        $existingUser = $supplier?->user;

        if (! $email && ! $existingUser) {
            return null;
        }

        if ($existingUser) {
            $payload = [
                'full_name' => $data['contact_person'] ?: $data['name'],
                'email' => $email ?: $existingUser->email,
                'role' => 'supplier',
                'account_status' => $data['status'] === 'active' ? 'active' : 'suspended',
            ];

            if ($password) {
                $payload['password'] = Hash::make($password);
            }

            $existingUser->update($payload);

            return $existingUser->id;
        }

        if (! $email || ! $password) {
            return null;
        }

        $user = User::where('email', $email)->first();

        if ($user && $user->role === 'admin') {
            throw ValidationException::withMessages([
                'email' => 'Email ini sudah dipakai akun admin, gunakan email supplier lain.',
            ]);
        }

        if ($user) {
            $user->update([
                'full_name' => $data['contact_person'] ?: $data['name'],
                'role' => 'supplier',
                'account_status' => $data['status'] === 'active' ? 'active' : 'suspended',
                'password' => Hash::make($password),
            ]);

            return $user->id;
        }

        $user = User::create([
            'name' => $this->uniqueUsername($data['name']),
            'full_name' => $data['contact_person'] ?: $data['name'],
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'supplier',
            'account_status' => $data['status'] === 'active' ? 'active' : 'suspended',
        ]);

        return $user->id;
    }

    private function uniqueUsername(string $name): string
    {
        $base = Str::slug($name, '_') ?: 'supplier';
        $candidate = $base;
        $counter = 1;

        while (User::where('name', $candidate)->exists()) {
            $candidate = $base . '_' . $counter;
            $counter++;
        }

        return $candidate;
    }
}

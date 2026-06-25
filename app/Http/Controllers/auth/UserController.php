<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Halaman login
    public function login()
    {
        return view('auth.login');
    }

    // Proses login dengan multiple role (Admin & User Reguler)
    public function doLogin(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        // Coba cari user di database berdasarkan email atau name
        $user = User::where('email', $username)->orWhere('name', $username)->first();

        if ($user && Hash::check($password, $user->password)) {
            if ($user->role !== 'admin' && $user->account_status !== 'active') {
                return back()->with('error', 'Akun Anda sedang dinonaktifkan. Silakan hubungi admin Motifnesia.');
            }

            // Login user menggunakan Laravel Auth
            Auth::login($user);
            
            if ($user->role === 'admin') {
                if ($user->admin_role === 'finance') {
                    return redirect()->route('admin.orders.status')->with('success', 'Berhasil login sebagai Admin Finance!');
                }
                if ($user->admin_role === 'gudang') {
                    return redirect()->route('admin.warehouse.index')->with('success', 'Berhasil login sebagai Admin Gudang!');
                }
                return redirect()->route('admin.product.management.index')->with('success', 'Berhasil login sebagai Admin!');
            } elseif ($user->role === 'supplier') {
                return redirect()->route('supplier.procurements.index')->with('success', 'Berhasil login sebagai Supplier!');
            } else {
                return redirect()->route('customer.home')->with('success', 'Berhasil login!');
            }
        }

        // Jika tidak ditemukan di DB, fallback ke akun dummy yang disimpan di session (atau default)
        $accounts = session('accounts', [
            [
                'username' => 'admin',
                'password' => '123456',
                'nama' => 'Admin Motifnesia',
                'role' => 'admin'
            ],
            [
                'username' => 'user',
                'password' => '456789',
                'nama' => 'Pelanggan Setia',
                'role' => 'user'
            ]
        ]);

        // Cari akun yang cocok di fallback
        $authenticatedAccount = null;
        foreach ($accounts as $akun) {
            if ($username === $akun['username'] && $password === $akun['password']) {
                $authenticatedAccount = $akun;
                break;
            }
        }

        if ($authenticatedAccount) {
            session(['user' => $authenticatedAccount]);
            if ($authenticatedAccount['role'] === 'admin') {
                return redirect()->route('admin.product.management.index')->with('success', 'Berhasil login sebagai Admin!');
            } else {
                return redirect()->route('customer.home')->with('success', 'Berhasil login!');
            }
        }

        return back()->with('error', 'Username atau password salah!');
    }

    // Halaman register (GET)
    public function register()
    {
        return view('auth.register');
    }

    // Proses register (POST)
    public function doRegister(Request $request)
    {
        // Validasi form sesuai fields di view
        $request->validate([
            'username' => 'required|min:3|unique:users,name',
            'full_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'secret_question' => 'required',
            'secret_answer' => 'required'
        ]);

        // Buat user baru di database, role default 'customer'.
        $user = User::create([
            'name' => $request->username,
            'full_name' => $request->full_name ?? null,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'secret_question' => $request->secret_question,
            'secret_answer' => $request->secret_answer,
        ]);

        if ($user) {
            return redirect()->route('auth.login')->with('success', 'Registrasi berhasil! Silakan login.');
        }

        return back()->with('error', 'Gagal mendaftar, silakan coba lagi.')->withInput();
    }

    // Halaman forgot password
    public function forgot()
    {
        return view('auth.forgot');
    }

    // Proses reset password
    public function doForgot(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'secret_question' => 'required',
            'secret_answer' => 'required',
            'new_password' => 'required|min:6',
            'new_password_confirmation' => 'required|same:new_password'
        ]);

        // Cari user berdasarkan username atau email
        $user = User::where('email', $request->username)
                    ->orWhere('name', $request->username)
                    ->first();

        if (!$user) {
            return back()->with('error', 'Username tidak ditemukan!')->withInput();
        }

        // Verifikasi pertanyaan dan jawaban rahasia
        if ($user->secret_question !== $request->secret_question) {
            return back()->with('error', 'Pertanyaan rahasia tidak cocok!')->withInput();
        }

        if (strtolower(trim($user->secret_answer)) !== strtolower(trim($request->secret_answer))) {
            return back()->with('error', 'Jawaban rahasia tidak cocok!')->withInput();
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('auth.login')->with('success', 'Password berhasil direset! Silakan login dengan password baru.');
    }

    // Logout
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('auth.login')->with('success', 'Berhasil logout.');
    }
}

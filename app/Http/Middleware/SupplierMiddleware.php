<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SupplierMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login sebagai supplier terlebih dahulu.');
        }

        $user = Auth::user();

        if ($user->role === 'admin') {
            $redirectUrl = match ($user->admin_role) {
                'finance' => '/admin/order-status',
                'gudang' => '/admin/warehouse',
                default => '/admin/product-management',
            };

            return redirect($redirectUrl)
                ->with('error', 'Admin tidak dapat mengakses portal supplier.');
        }

        if ($user->role !== 'supplier') {
            return redirect()->route('customer.home')
                ->with('error', 'Halaman supplier hanya untuk akun supplier.');
        }

        if (($user->account_status ?? 'active') !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('auth.login')
                ->with('error', 'Akun supplier sedang dinonaktifkan.');
        }

        return $next($request);
    }
}

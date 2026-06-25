<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek apakah user adalah admin (admin tidak bisa akses customer routes)
        if (Auth::user()->role === 'admin') {
            $redirectUrl = match (Auth::user()->admin_role) {
                'finance' => '/admin/order-status',
                'gudang' => '/admin/warehouse',
                default => '/admin/product-management',
            };
            return redirect($redirectUrl)
                ->with('error', 'Admin tidak dapat mengakses halaman customer.');
        }

        if (Auth::user()->role === 'supplier') {
            return redirect()->route('supplier.procurements.index')
                ->with('error', 'Supplier tidak dapat mengakses halaman customer.');
        }

        if (Auth::user()->account_status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('auth.login')
                ->with('error', 'Akun Anda sedang dinonaktifkan. Silakan hubungi admin Motifnesia.');
        }

        return $next($request);
    }
}

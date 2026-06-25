<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
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

        if (Auth::user()->role === 'supplier') {
            return redirect()->route('supplier.procurements.index')
                ->with('error', 'Supplier tidak dapat mengakses halaman admin.');
        }

        // Cek apakah user adalah customer (customer tidak bisa akses admin routes)
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('customer.home')
                ->with('error', 'Customer tidak dapat mengakses halaman admin.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BlockAdminFromCustomerMiddleware
{
    /**
     * Handle an incoming request.
     * Block admin dari mengakses halaman customer public
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user adalah admin, redirect ke admin dashboard yang sesuai
        if (Auth::check() && Auth::user()->role === 'admin') {
            $redirectUrl = Auth::user()->admin_role === 'finance' ? '/admin/order-status' : '/admin/product-management';
            return redirect($redirectUrl)
                ->with('error', 'Admin tidak dapat mengakses halaman customer.');
        }

        return $next($request);
    }
}

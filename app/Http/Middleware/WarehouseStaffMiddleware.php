<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WarehouseStaffMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isWarehouseStaff()) {
            abort(403, 'Akses ditolak. Hanya warehouse staff yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
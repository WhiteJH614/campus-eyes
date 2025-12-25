<?php
// Author: Lee Jia Hui
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTechnician
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        if (Auth::user()->role !== 'Technician') {
            abort(403, 'Access denied. Technician role required.');
        }

        return $next($request);
    }
}

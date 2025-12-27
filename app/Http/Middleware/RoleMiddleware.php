<?php
// Author: Tan Jun Yan
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Check if the authenticated user has one of the allowed roles.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Allowed roles (Reporter, Technician, Admin)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login');
        }

        if (!in_array($request->user()->role, $roles)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403, 'Unauthorized. You do not have permission to access this page.');
        }

        return $next($request);
    }
}

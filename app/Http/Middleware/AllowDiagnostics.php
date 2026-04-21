<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Chỉ cho phép kiểm tra kết nối DB khi local hoặc APP_DEBUG=true.
 */
class AllowDiagnostics
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->environment('local') && ! config('app.debug')) {
            abort(404);
        }

        return $next($request);
    }
}

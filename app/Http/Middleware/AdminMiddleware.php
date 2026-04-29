<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('platform.login');
        }

        if (!auth()->user()->inRole('admin')) {
            abort(403, 'Доступ запрещен. Требуется роль администратора.');
        }

        return $next($request);
    }
}

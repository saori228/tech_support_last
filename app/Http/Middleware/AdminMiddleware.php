<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Добавляем логирование для отладки
        Log::info('AdminMiddleware: Checking if user is admin', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role->name ?? 'no role',
            'is_admin_method' => auth()->user()->isAdmin(),
        ]);
        
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'У вас нет прав для доступа к этой странице');
        }
        
        return $next($request);
    }
}
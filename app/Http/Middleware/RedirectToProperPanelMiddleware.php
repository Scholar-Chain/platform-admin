<?php

namespace App\Http\Middleware;

use Closure;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Filament\Pages\Dashboard;
use Symfony\Component\HttpFoundation\Response;

class RedirectToProperPanelMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->hasRole([UserRole::SUPER_ADMIN->value])) {
            return redirect()->to(Dashboard::getUrl(panel: 'admin'));
        }
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log; // Jangan lupa import Log

class VerySimpleTestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('VERY SIMPLE TEST MIDDLEWARE RUNNING - Path: ' . $request->path() . ' - Port: ' . $request->getPort());
        return $next($request);
    }
}
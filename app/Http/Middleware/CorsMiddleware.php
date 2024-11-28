<?php

// app/Http/Middleware/CorsMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class CorsMiddleware
{
    public function handle($request, Closure $next)
    {
        Log::info('Adding CORS headers...');
        
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*') 
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS') 
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization') 
        
            ->header('Cross-Origin-Opener-Policy', 'unsafe-none')
            ->header('Cross-Origin-Embedder-Policy', 'unsafe-none');
        
    }
}
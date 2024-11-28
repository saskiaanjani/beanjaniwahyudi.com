<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Storage;

class ServeStorageFile
{
    public function handle($request, Closure $next)
    {
        $path = storage_path('app/public/' . $request->path());

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
}


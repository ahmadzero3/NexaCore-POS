<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstallationStatusCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if app is installed using both methods
        $envInstalled = env('INSTALLATION_STATUS', false) === 'true' || env('INSTALLATION_STATUS', false) === true;
        $fileInstalled = file_exists(storage_path('installed'));
        
        // If not installed by either method, redirect to installer
        if (!$envInstalled || !$fileInstalled) {
            return redirect('/install');
        }

        return $next($request);
    }


}

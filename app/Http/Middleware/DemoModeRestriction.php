<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoModeRestriction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        /**
         * Demo Mode Code
         * */
        if (config('demo.enabled') && ($request->isMethod('PUT') || $request->isMethod('POST'))) {
            $restricted_routes = [
                // Route URI strings or patterns
                'general.store',
                'logo.store',
                'company.update',
                'prefix.update',

                'language.update',
                'language.delete',

                'tax.update',
                'tax.delete',

                'payment.type.update',
                'payment.type.store',

                'permission.group.update',
                'permission.group.delete',

                'permission.update',
                'permission.delete',

                'role.update',
                'role.delete',

                'user.update',
                'user.delete',

                'user.profile.update',
                'user.profile.password',
                
            ];

            if (in_array($request->route()->getName(), $restricted_routes) || in_array(url()->current(), $restricted_routes)) {
                return response()->json([
                    'status'  => false,
                    'message' => __('app.demo_mode_restricted'),
                ], 409);
                //return Redirect::back()->with('error', 'Updates are disabled in demo mode.');
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
       if (! $request->expectsJson()) {
            if ($request->is('api/*')) {
                return response()->json(['status'=>false,'message' => 'Unauthenticated.'], 401);
            }
            if($request->routeIs('admin.*')) {
                return route('admin.login');
            }
            if($request->routeIs('seller.*')) {
                return route('seller.login');
            }
            return route('seller.login');
        }
    }

}

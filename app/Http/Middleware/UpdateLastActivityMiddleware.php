<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class UpdateLastActivityMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = User::find(Auth::id());
            $user->last_activity_at = Carbon::now();
            $user->save();
        }
        return $next($request);
    }
}
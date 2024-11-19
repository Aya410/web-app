<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckGroupAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = Auth::id();
        $user = User::find($id);
        $exists = Admin::where('user_id', $id)->exists();
        if($user->role == 'user' &&  $exists)
        {
            return $next($request);
        }

        return response()->json('could not acsses to this route');
    }
}

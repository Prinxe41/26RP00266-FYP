<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role 
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Check if user has any role
        if (!Session::has('user_role')) {
            return redirect()->route('login');
        }

        // Check if user has the specified role
        if (Session::get('user_role') !== $role) {
            return redirect()->route('login')->withErrors(['login' => 'Unauthorized access.']);
        }

        // Additional checks based on role
        switch ($role) {
            case 'student':
                if (!Session::has('student_data')) {
                    //Auth::logout();
                    Session::flush();
                    return redirect()->route('login');
                }
                break;

            case 'hod':
                if (!Session::has('hod_data')) {
                    Session::flush();
                    return redirect()->route('login');
                }
                break;

            case 'supervisor':
                if (!Session::has('supervisor_data')) {
                    Session::flush();
                    return redirect()->route('login');
                }
                break;
        }

        return $next($request);
    }
}
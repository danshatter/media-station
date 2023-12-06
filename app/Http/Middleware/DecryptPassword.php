<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Application as ApplicationService;

class DecryptPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        /**
         * We get the password strings
         */
        $currentPassword = $request->input('current_password');
        $password = $request->input('password');
        $passwordConfirmation = $request->input('password_confirmation');

        /**
         * We check for any password inputs of the application
         */
        if (isset($currentPassword)) {
            $request->merge([
                'current_password' => app()->make(ApplicationService::class)->decryptPasswordString($currentPassword)
            ]);
        }

        if (isset($password)) {
            $request->merge([
                'password' => app()->make(ApplicationService::class)->decryptPasswordString($password)
            ]);
        }

        if (isset($passwordConfirmation)) {
            $request->merge([
                'password_confirmation' => app()->make(ApplicationService::class)->decryptPasswordString($passwordConfirmation)
            ]);
        }

        return $next($request);
    }
}

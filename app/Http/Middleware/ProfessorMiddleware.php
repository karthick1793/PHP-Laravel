<?php

namespace App\Http\Middleware;

use App\Traits\CryptTrait;
use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProfessorMiddleware
{
    use CryptTrait, HttpResponses;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accessToken = $request->bearerToken() ?? '';
        $decryptedToken = $this->decryptOrReturnOriginal($accessToken);
        $request->headers->set('Authorization', "Bearer $decryptedToken");
        if (Auth::guard('professor')->user() === null) {
            return $this->error('Unauthenticated', [], 401);
        }
        date_default_timezone_set('Asia/Kolkata');

        return $next($request);
    }
}

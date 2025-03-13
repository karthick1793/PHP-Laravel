<?php

namespace App\Services;

use App\Models\Professor;
use App\Traits\CryptTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth as JWTAuthFacade;
use Tymon\JWTAuth\JWTAuth;

class JWTService
{
    use CryptTrait;

    public function __construct(protected JWTAuth $jWTAuth) {}

    /**
     * Generate JWT token based on the guard
     *
     * @return string|null
     */
    public function accessToken(string $guard, array $credentials, $claims = [], $shouldExpire = true)
    {
        Auth::shouldUse($guard);

        $user = $this->getUserForGuard($guard, $credentials);
        if (! $user) {
            return null;
        }
        $claims['token'] = $user->token;
        if (! $shouldExpire) {
            $claims['exp'] = strtotime('+100 years');
        }
        $token = $this->jWTAuth->claims($claims)->fromUser($user);

        return $this->encryptInputString($token);
    }

    /**
     * Get user data or return null
     *
     * @return Professor|object|\Illuminate\Contracts\Auth\Authenticatable|\Illuminate\Database\Eloquent\Model|null
     */
    public function getUserForGuard(string $guard, array $credentials)
    {
        if ($guard == 'professor') {
            return Professor::where($credentials)->first();
        }
        if (Auth::attempt($credentials)) {
            return Auth::user();
        }

        return null;
    }

    /**
     * Invalidate the current token.
     *
     * @return void
     */
    public function invalidateToken()
    {
        JWTAuthFacade::invalidate(JWTAuthFacade::getToken());
    }

    /**
     * Get JWT
     *
     * @return string|null
     */
    public function getAccessToken(Request $request)
    {
        return $request->bearerToken();
    }

    /**
     * Get payload of JWT
     *
     * @return \Tymon\JWTAuth\Payload
     */
    public function getJWTPayload($token)
    {
        $payload = $this->jWTAuth->setToken($token)->payload();

        return $payload;
    }

    public function getUserToken($token)
    {
        return $this->getJWTPayload($token)['token'];
    }

    public function getUser($guard)
    {
        return Auth::guard($guard)->user();
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Auth extends Authenticate
{
    protected function authenticate(array $guards)
    {

        if ($this->auth->guard('api')->check()) {
            return $this->auth->shouldUse('api');
        }

        throw new UnauthorizedHttpException('未登录', 'Unauthenticated');
    }
}

<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function unauthenticated($request, array $guards)
    {
        throw new AuthenticationException(
            '未登录 或 登陆状态已过期.', $guards, $this->redirectTo($request)
        );
    }

    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return response()->json('未登录', 1);
        }
    }
}

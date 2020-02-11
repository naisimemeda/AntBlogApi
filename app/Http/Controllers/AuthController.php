<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\PassportToken;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use PassportToken;
    /*
     * 注册用户
     */
    public function store(UserRequest $request, AuthManager $auth)
    {
        $user   = User::query()->create($request->only(['email', 'name', 'password']));
        $result = $this->getBearerTokenByUser($user, '1', false);
        return $this->success($result);
    }

    /**
     * 获取令牌
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        Auth::guard()->attempt([
            'email' => $request->input('username'),
            'password' => $request->input('password')
        ]);

        $user = Auth::user();
        if (!$user) {
            return $this->failed('账号 或 密码错误');
        }
        $token = $this->getBearerTokenByUser($user, 1, false);

        return $this->success(compact('token', 'user'));
    }

    /**
     * 退出
     */
    public function logout()
    {
        if (Auth::guard('api')->check()) {
            Auth::guard('api')->user()->token()->delete();
        }
        return $this->success('成功');
    }
}

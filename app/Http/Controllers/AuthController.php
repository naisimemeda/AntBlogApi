<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Zend\Diactoros\Response as Psr7Response;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends Controller
{
    /*
     * 注册用户
     */
    public function store(UserRequest $request, AuthManager $auth)
    {
        $user = User::query()->create($request->only(['email', 'name', 'password']));
        $token = $user->createToken('api')->accessToken;
        return $this->success('Bearer ' . $token);
    }

    /**
     * 获取令牌
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->get('username'),
            'password' => $request->get('password')
        ];

        if (Auth::guard()->attempt($credentials)) {
            $token = Auth::guard()->user()->createToken('api')->accessToken;
            return $this->success('Bearer ' . $token);
        } else {
            return $this->failed('UnAuthorised');
        }
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

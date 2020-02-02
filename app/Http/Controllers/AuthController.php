<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Auth\AuthManager;
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
        $id = User::query()->create($request->only(['email', 'name', 'password']));
//        $token = app('auth')->guard('api')->setUser(User::find($id));
//        return $this->success($token);
    }

    public function login(AuthorizationRequest $originRequest, AuthorizationServer $server, ServerRequestInterface $serverRequest)
    {
        try {
            return $server->respondToAccessTokenRequest($serverRequest, new Psr7Response)->withStatus(201);
        } catch (OAuthServerException $e) {
            dd($e->getMessage());
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

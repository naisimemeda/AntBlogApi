<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\PassportToken;
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
    use PassportToken;
    /*
     * 注册用户
     */
    public function store(UserRequest $request, AuthManager $auth)
    {
        $user = User::query()->create($request->only(['email', 'name', 'password']));
        $result = $this->getBearerTokenByUser($user, '1', false);
        return $this->success($result);
    }

    /**
     * 获取令牌
     * @param Request $request
     * @param AuthorizationRequest $originRequest
     * @param AuthorizationServer $server
     * @param ServerRequestInterface $serverRequest
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function login(Request $request,
                          AuthorizationRequest $originRequest,
                          AuthorizationServer $server,
                          ServerRequestInterface $serverRequest)
    {
        try {
            return $server->respondToAccessTokenRequest($serverRequest, new Psr7Response)->withStatus(201);
        } catch(OAuthServerException $e) {
            return $this->failed($e->getMessage());
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

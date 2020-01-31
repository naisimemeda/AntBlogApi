<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Zend\Diactoros\Response as Psr7Response;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Http\Message\ServerRequestInterface;
class AuthController extends Controller
{
    public function login(AuthorizationRequest $originRequest, AuthorizationServer $server, ServerRequestInterface $serverRequest)
    {
        try {
            return $server->respondToAccessTokenRequest($serverRequest, new Psr7Response)->withStatus(201);
        } catch(OAuthServerException $e) {
            dd($e->getMessage());
        }
    }


    public function store(Request $request){
        User::query()->create($request->all());
        $token = Auth::guard('api')->attempt(['email' => $request->input('email'), 'password'=>$request->password]);
        return response()->json(
            [
                'token' => 'bearer ' . $token
            ]
        );
    }
}

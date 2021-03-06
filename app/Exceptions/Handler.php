<?php

namespace App\Exceptions;

use App\Common\Toast;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    public function render($request, Exception $exception)
    {
        if ($exception instanceof ValidationException) {
            // 只读取错误中的第一个错误信息
            $errors = $exception->errors();
            $message = '';
            // 框架返回的是二维数组，因此需要去循环读取第一个数据
            foreach ($errors as $key => $val) {
                $keys = array_key_first($val);
                $message = $val[$keys];
                break;
            }
            return Response()->json(['message' => $message], 401);
        }

        if ($exception instanceof AuthorizationException) {
            return Response()->json(['message' => '没有该权限'], 403);
        }

        if ($exception instanceof ModelNotFoundException) {
            return Response()->json(['message' => '不要搞事情哦'], 403);
        }


        return parent::render($request, $exception);
    }
}

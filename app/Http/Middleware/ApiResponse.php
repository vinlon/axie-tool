<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApiResponse
{
    /**
     * Handle an incoming request.
     * api接口数据返回结果内容的处理.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $e = $response->exception;

        $code = 'ok';
        $httpStatus = 200;
        $message = 'ok';
        $data = null;
        $trace = null;
        if ($e) {
            $code = 'fail';
            if ($e instanceof ApiException) {
                $message = $e->getMessage();
            } else {
                if ($e instanceof ValidationException) {
                    //参数错误
                    $message = '参数错误';
                    $trace['errors'] = $e->errors();
                } else {
                    //未知错误
                    $code = 'unknown';
                    $httpStatus = 500;
                    if (config('app.debug', false)) {
                        $message = $e->getMessage();
                        $trace['debug'] = $e->getTrace();
                    } else {
                        $message = '服务器错误';
                    }
                }
            }
        } else {
            if ($response instanceof JsonResponse) {
                $data = $response->getData();
            }
        }

        $response = new JsonResponse();
        $response->setData([
            'code' => $code,
            'msg' => $message,
            'data' => $data,
            'trace' => $trace,
        ])->setStatusCode($httpStatus);

        return $response;
    }
}

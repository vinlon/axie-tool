<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class XssDefense
{
    protected $except = [
        //注：跳过密码和富文本字段
        '/lay-admin/init' => ['password'],
        '/lay-admin/password_login_captcha' => ['password'],
        '/lay-admin/reset_password' => ['new_password'],
        '/lay-admin/users/resetPassword' => ['password', 'verify_password'],
        '/lay-admin/users' => ['password', 'verify_password'],
        '/lay-admin/changePassword' => ['old_password', 'new_password', 'verify_password'],
    ];

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $uri = $request->getRequestUri();
        $exceptFields = Arr::get($this->except, $uri, []);
        $input = $request->all();
        foreach ($input as $key => &$value) {
            if (!in_array($key, $exceptFields)) {
                $value = htmlspecialchars($value);
            }
        }
        $request->merge($input);

        return $next($request);
    }
}

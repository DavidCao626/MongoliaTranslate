<?php

namespace App\Http\Middleware;

use Closure;

class WechatVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') == false) {
            die("请在微信手机客户端中打开");
        }
        return $next($request);
    }
}

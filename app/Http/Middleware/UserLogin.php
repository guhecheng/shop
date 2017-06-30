<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\IndexController;

class UserLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    private $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxd8b6b83c91c44ac3&redirect_uri=http%3A%2F%2Fwww.jingyuxuexiao.com%2F&response_type=code&scope=snsapi_userinfo&state=cd16a43a0a6e5b6d007c942c8850a111#wechat_redirect';
    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('uid')) {
            return redirect($this->url);
        }
        return $next($request);
    }
}

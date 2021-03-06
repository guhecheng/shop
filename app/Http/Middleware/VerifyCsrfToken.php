<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'address/setdefault',
        '/admin/card',
        '/card/notify',
        '/order/wxnotify',
        '/wx',
        '/purchase/notify',
        '/purchase/wxnotify',
        '/user/recvcoupon'
    ];
}

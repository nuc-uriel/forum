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
        'member/head/set',
        'member/head/save',
        'member/introduce/set',
        'group/icon/save',
        'group/icon/set',
        'group/icon/set',
        'chat/send',
        'report',
    ];
}

<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Middleware\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * Trust semua proxy (Cloudflare)
     */
    protected $proxies = '*';

    /**
     * Header yang digunakan
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}

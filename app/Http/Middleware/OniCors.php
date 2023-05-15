<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OniCors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $allowedOrigins = ['http://localhost:3000', 'https://onilist.club'];
        // $origin = $_SERVER['HTTP_ORIGIN'];

            $response = $next($request);
            $response->header('Access-Control-Allow-Origin', "https://onilist.club");
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');


        return $response;
    }
}

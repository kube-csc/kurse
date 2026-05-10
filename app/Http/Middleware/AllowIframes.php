<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowIframes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Erlaube das Einbetten in IFrames von fremden Domains
        // Wir entfernen X-Frame-Options, falls gesetzt, oder setzen Content-Security-Policy
        if (method_exists($response, 'header')) {
            $response->header('X-Frame-Options', 'ALLOWALL'); // Oder einfach entfernen
            // Modernerer Weg via CSP
            $response->header('Content-Security-Policy', "frame-ancestors *;");
        }

        return $response;
    }
}

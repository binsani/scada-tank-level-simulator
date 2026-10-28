<?php

namespace App\Http\Middleware;

use App\Support\DemoVisitorContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class IdentifyDemoVisitor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('demo.visitor_isolation_enabled', false)) {
            return $next($request);
        }

        $context = DemoVisitorContext::fromRequest($request);
        $request->attributes->set('scada_demo_visitor', $context['id']);
        $request->attributes->set('scada_demo_state_key', $context['state_key']);

        $response = $next($request);
        if ($context['new']) {
            $response->headers->setCookie(DemoVisitorContext::cookie($context['id'], $request));
        }

        return $response;
    }
}

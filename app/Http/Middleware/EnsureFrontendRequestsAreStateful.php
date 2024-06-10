<?php

namespace App\Http\Middleware;

use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful as Middleware;

class EnsureFrontendRequestsAreStateful extends Middleware
{
    protected function configureSecureCookieSessions()
    {
        parent::configureSecureCookieSessions();

        config([
            'session.same_site' => 'none',
            'session.secure' => true,
        ]);
    }

    public static function fromFrontend($request)
    {
        $fromFrontend = parent::fromFrontend($request);
        if ($fromFrontend) {
            $url = $request->headers->get('referer') ?: $request->headers->get('origin');
            $parsedUrl = parse_url($url);
            config()->set('session.domain', $parsedUrl['host']);
        }

        return $fromFrontend;
    }
}

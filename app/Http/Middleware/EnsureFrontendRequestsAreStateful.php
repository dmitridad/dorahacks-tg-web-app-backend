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
}

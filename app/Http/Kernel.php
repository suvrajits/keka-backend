<?php

protected $middlewareGroups = [
    'api' => [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
	\App\Http\Middleware\VerifyCsrfToken::class,
        'throttle:api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
];

protected $routeMiddleware = [
    'auth.admin' => \App\Http\Middleware\RedirectIfNotAdmin::class,
];


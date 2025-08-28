<?php

protected $middlewareGroups = [
    'api' => [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
	\App\Http\Middleware\VerifyCsrfToken::class,
        'throttle:api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],

    'web' => [
        \App\Http\Middleware\Authenticate::class,
        \App\Http\Middleware\RedirectIfNotAdmin::class,
    ],
];

protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.admin' => \App\Http\Middleware\RedirectIfNotAdmin::class,
];
'api' => [
    \Tymon\JWTAuth\Middleware\Authenticate::class,
    \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
],
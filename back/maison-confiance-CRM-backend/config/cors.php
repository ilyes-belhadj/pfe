<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paths to be allowed for CORS
    |--------------------------------------------------------------------------
    |
    | Define the paths that should be allowed for CORS. You can use '*' as a
    | wildcard to allow all paths or specify specific paths, such as 'api/*'.
    |
    */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
    |--------------------------------------------------------------------------
    | Allowed HTTP Methods
    |--------------------------------------------------------------------------
    |
    | Define the HTTP methods that are allowed for CORS requests. You can use
    | '*' to allow all methods, or specify an array of allowed methods such
    | as ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'].
    |
    */
    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Define the origins that are allowed to make CORS requests. Use '*' to
    | allow all origins, or specify an array of allowed origins.
    |
    | You can also allow subdomains with patterns like 'https://*.example.com'.
    |
    */
    'allowed_origins' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | Patterns to match against the request origin. Use this option if you need
    | to allow requests from a dynamic set of origins.
    |
    */
    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Define the headers that are allowed to be sent in the request. You can
    | use '*' to allow all headers or specify an array of allowed headers.
    |
    */
    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | These headers will be made accessible to the browser via the 'Access-Control-Expose-Headers'
    | header in the response.
    |
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | The 'Access-Control-Max-Age' header indicates how long the response to a
    | preflight request can be cached.
    |
    */
    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Set to true to allow cookies or other credentials to be included in the
    | CORS requests. This must be set to true if you want to use cookies in CORS.
    |
    */
    'supports_credentials' => false,
    'paths' => ['api/*'],
'allowed_origins' => ['http://localhost:4200'],


];

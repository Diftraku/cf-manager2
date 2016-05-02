<?php
// Application middleware

if (CF_PROXY_CHECKIP) {
    $app->add(new RKA\Middleware\IpAddress(true));
}

if (CF_AUTH_HTTP) {
    use Slim\Middleware\HttpBasicAuthentication\PdoAuthenticator;
    use Slim\Middleware\HttpBasicAuthentication;

    $app->add(new HttpBasicAuthentication([
        'path' => '/*',
        'realm' => CF_AUTH_HTTP_REALM,
        'authenticator' => new PdoAuthenticator([
            'pdo' => R::getPDO(),
            'table' => CF_DB_TBL_PREFIX.CF_DB_TBL_USERS,
            'user' => 'username',
            'hash' => 'password'
        ])
    ]));
}

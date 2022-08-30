<?php

namespace App\Http\Controllers\Test;

use function view;

class KeycloakFrontController
{
    public const EXCHANGE_VERIFIER = '1234';

    public const XSESSION = 'alEKeAHvvhvKp19g608yJdK9R0hwOopK3XA6TQ7P';

    public function index()
    {
        if ('prod' == env('APP_ENV')) {
            abort(404);
        }

        return view('test.keycloak', [
            'url' => '/keycloak/auth',
            'refreshUrl' => '/keycloak/refresh',
            'revokeUrl' => '/keycloak/revoke',
            'getTokenUrl' => '/keycloak/token',
            'vodafoneCustomer' => 'http://localhost/oauth/keycloak/token/1?exchange_verifier_challenge=' . self::EXCHANGE_VERIFIER . '&vodafone_session=' . self::XSESSION,
            'scope' => 'openid profile email microprofile-jwt roles',
            'refreshToken' => '',
            'accessToken' => '',
            'exchangeVerifier' => self::EXCHANGE_VERIFIER,
            'xsession' => self::XSESSION,
        ]);
    }
}

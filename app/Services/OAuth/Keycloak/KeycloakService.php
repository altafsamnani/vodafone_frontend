<?php

namespace App\Services\OAuth\Keycloak;

use App\Services\Contract\OauthServiceInterface;
use App\Services\OAuthService;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Session\Session;
use Laravel\Socialite\Contracts\Factory;
use SocialiteProviders\Keycloak\Provider;

class KeycloakService extends OAuthService implements OauthServiceInterface
{
    private const DRIVER = 'keycloak';

    private Factory $socialite;

    public function __construct(Session $session, Repository $cache, Factory $socialite)
    {
        parent::__construct($session, $cache);
        $this->socialite = $socialite;
    }

    public function getRedirectUrl(): string
    {
        $user = $this->getDriver()->user();
        dd($user);
        $token = $user->accessTokenResponseBody;

        return $this->getSessionUrl($token);
    }

    public function getAuthenticateUrl($requestParameters): string
    {
        $this->setVerifier($requestParameters);

        return $this->getDriver()->setScopes($requestParameters['scope'])->redirect()->getTargetUrl();
    }

    public function getRefreshToken(string $refreshToken): mixed
    {
        return $this->getDriver()->refresh($refreshToken);
    }

    public function revokeToken(string $refreshToken, ?string $accessToken = null): mixed
    {
        return $this->getDriver()->revoke($refreshToken, $accessToken);
    }

    protected function getDriver(): Provider
    {
        return $this->socialite->driver(self::DRIVER);
    }
}

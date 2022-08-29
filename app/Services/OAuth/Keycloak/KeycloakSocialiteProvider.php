<?php

namespace App\Services\OAuth\Keycloak;

use GuzzleHttp\RequestOptions;
use Laravel\Socialite\Two\InvalidStateException;
use SocialiteProviders\Keycloak\Provider;
use SocialiteProviders\Manager\OAuth2\User;

class KeycloakSocialiteProvider extends Provider
{
    protected $scopes = [];

    public function refresh(string $refreshToken): array
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            RequestOptions::FORM_PARAMS => $this->getFields($refreshToken),
        ]);

        return json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    public function revoke(string $refreshToken, ?string $accessToken = null): mixed
    {
        if (!$accessToken) {
            $tokenResponse = $this->refresh($refreshToken);
            $accessToken = $tokenResponse['access_token'];
        }
        $response = $this->getHttpClient()->post($this->getRevokeUrl(), [
            RequestOptions::HEADERS => ['Authorization' => 'Bearer ' . $accessToken],
        ]);

        return json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    public function getFields(string $refreshToken): array
    {
        return [
            'tenant' => 'common',
            'client_id' => $this->clientId,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_secret' => $this->clientSecret,
        ];
    }

    public function user(): mixed
    {
        dd($this->getAccessTokenResponse($this->getCode()));
        if ($this->user) {
            return $this->user;
        }

        if ($this->hasInvalidState()) {
            throw new InvalidStateException();
        }

        $response = $this->getAccessTokenResponse($this->getCode());
        $this->credentialsResponseBody = $response;

        $this->user = new User();

        $token = $this->parseAccessToken($response);
        $this->user->setAccessTokenResponseBody($this->credentialsResponseBody);

        return $this->user->setToken($token)
            ->setRefreshToken($this->parseRefreshToken($response))
            ->setExpiresIn($this->parseExpiresIn($response));
    }

    protected function getBaseUrl()
    {
        return rtrim(rtrim('http://localhost:85/auth', '/').'/realms/'.$this->getConfig('realms', 'master'), '/');
    }

    private function getRevokeUrl(): string
    {
        return $this->graphUrl . '/microsoft.graph.revokeSignInSessions';
    }


}

<?php

namespace Tests\Unit\Services\OAuth\Keycloak;

use App\Services\OAuth\Keycloak\KeycloakSocialiteProvider;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @coversDefaultClass \App\Services\OAuth\Keycloak\KeycloakSocialiteProvider
 */
class KeycloakSocialiteProviderTest extends TestCase
{
    /** @var string ACCESS_TOKEN */
    private const ACCESS_TOKEN = 'eyzdysysyazz_Access';

    /** @var string REFRESH_TOKEN */
    private const REFRESH_TOKEN = 'eysaazzzdysysyazz_Refresh';

    /** @var string */
    private const ERROR_MESSAGE = 'Client connection error';

    private KeycloakSocialiteProvider $keycloakProvider;

    private ClientInterface $client;

    /** @covers ::refresh */
    public function testkeycloakProviderFetchRefreshToken()
    {
        $responseInterface = $this->createMock(ResponseInterface::class);
        $responseInterface->method('getBody')
            ->willReturn(json_encode(['access_token' => self::ACCESS_TOKEN, 'refresh_token' => self::REFRESH_TOKEN]));

        $this->client = $this->createMock(Client::class);
        $this->client->expects(self::once())->method('post')->willReturn($responseInterface);

        $this->keycloakProvider = $this->createPartialMock(KeycloakSocialiteProvider::class, [ 'getHttpClient']);
        $this->keycloakProvider->expects($this->once())->method('getHttpClient')->willReturn($this->client);
        $response = $this->keycloakProvider->refresh(self::REFRESH_TOKEN);

        $this->assertArrayHasKey('access_token', $response);
        $this->assertEquals(self::ACCESS_TOKEN, $response['access_token']);

        $this->assertArrayHasKey('refresh_token', $response);
        $this->assertEquals(self::REFRESH_TOKEN, $response['refresh_token']);
    }

    /** @covers ::refresh */
    public function testkeycloakProviderFetchRefreshTokenError()
    {
        $requestInterface = $this->createMock(RequestInterface::class);
        $this->client = $this->createMock(Client::class);
        $this->client->expects(self::once())->method('post')
            ->willThrowException(new ConnectException(self::ERROR_MESSAGE, $requestInterface));
        $this->keycloakProvider = $this->createPartialMock(KeycloakSocialiteProvider::class, [ 'getHttpClient']);
        $this->keycloakProvider->expects($this->once())->method('getHttpClient')->willReturn($this->client);

        $this->expectException(ConnectException::class);
        $this->keycloakProvider->refresh(self::REFRESH_TOKEN);
    }

    /** @covers ::revokeAccessToken */
    public function testkeycloakProviderRevokeToken()
    {
        $responseInterface = $this->createMock(ResponseInterface::class);
        $responseInterface->method('getBody')
            ->willReturn(json_encode([
                '@odata.context' => 'https:\/\/graph.keycloak.com\/v1.0\/$metadata#Edm.Boolean',
                'value' => true,
            ]));

        $this->client = $this->createMock(Client::class);
        $this->client->expects(self::once())->method('post')->willReturn($responseInterface);

        $this->keycloakProvider = $this->createPartialMock(KeycloakSocialiteProvider::class, [ 'getHttpClient']);
        $this->keycloakProvider->expects($this->once())->method('getHttpClient')->willReturn($this->client);
        $response = $this->keycloakProvider->revoke(self::REFRESH_TOKEN, self::ACCESS_TOKEN);

        $this->assertArrayHasKey('@odata.context', $response);
        $this->assertArrayHasKey('value', $response);
        $this->assertIsBool($response['value']);
    }

    /** @covers ::revokeAccessToken */
    public function testkeycloakProviderRevokeTokenError()
    {
        $requestInterface = $this->createMock(RequestInterface::class);
        $this->client = $this->createMock(Client::class);
        $this->client->expects(self::once())->method('post')
            ->willThrowException(new ConnectException(self::ERROR_MESSAGE, $requestInterface));

        $this->keycloakProvider = $this->createPartialMock(KeycloakSocialiteProvider::class, [ 'getHttpClient']);
        $this->keycloakProvider->expects($this->once())->method('getHttpClient')->willReturn($this->client);

        $this->expectException(ConnectException::class);
        $this->keycloakProvider->revoke(self::REFRESH_TOKEN, self::ACCESS_TOKEN);
    }
}

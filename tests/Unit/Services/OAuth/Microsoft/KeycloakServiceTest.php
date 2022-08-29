<?php

namespace Tests\Unit\Services\OAuth\Keycloak;

use App\Services\OAuth\Keycloak\KeycloakSocialiteProvider;
use App\Services\OAuth\Keycloak\KeycloakService;
use Exception;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Session\Session;
use Laravel\Socialite\Contracts\Factory;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Services\OAuth\Keycloak\KeycloakService
 */
class KeycloakServiceTest extends TestCase
{
    /** @var string REFRESH_TOKEN */
    private const REFRESH_TOKEN = 'eysaazzzdysysyazz_Refresh';

    /** @var string ACCESS_TOKEN */
    private const ACCESS_TOKEN = 'eysaazzzdysysyazz_Access';

    /** @var string */
    private const ERROR_MESSAGE = 'There is an error';

    private KeycloakSocialiteProvider $keycloakProvider;

    private KeycloakService $keycloakService;

    private Session $session;

    private Repository $cache;

    private Factory $socialite;

    public function setUp(): void
    {
        $this->keycloakProvider = $this->createMock(KeycloakSocialiteProvider::class);

        $this->session = $this->createMock(Session::class);

        $this->cache = $this->createMock(Repository::class);

        $this->socialite = $this->createMock(Factory::class);

        $this->keycloakService = new KeycloakService($this->session, $this->cache, $this->socialite);
    }

    /** @covers ::getRefreshToken */
    public function testRefreshTokenIsReturnedSuccessfully()
    {
        $this->keycloakProvider->expects(self::once())->method('refresh')
            ->with(self::REFRESH_TOKEN)
            ->willReturn(['refresh_token' => self::REFRESH_TOKEN]);

        $this->socialite->expects(self::once())->method('driver')
            ->with('azure')
            ->willReturn($this->keycloakProvider);

        $response = $this->keycloakService->getRefreshToken(self::REFRESH_TOKEN);

        $this->assertEquals(self::REFRESH_TOKEN, $response['refresh_token']);
    }

    /** @covers ::getRefreshToken */
    public function testRefreshTokenThrowsError()
    {
        $this->keycloakProvider->expects(self::once())->method('refresh')
            ->with(self::REFRESH_TOKEN)
            ->willThrowException(new Exception(self::ERROR_MESSAGE));
        $this->socialite->expects(self::once())->method('driver')
            ->with('azure')
            ->willReturn($this->keycloakProvider);

        $this->expectException(Exception::class);

        $this->keycloakService->getRefreshToken(self::REFRESH_TOKEN);
    }

    /** @covers ::revokeToken */
    public function testRevokeTokenWithRefreshToken()
    {
        $this->keycloakProvider->expects(self::once())->method('revoke')
            ->with(self::REFRESH_TOKEN)
            ->willReturn(['@odata.context' => 'https:\/\/graph.keycloak.com\/v1.0\/$metadata#Edm.Boolean', 'value' => true]);
        $this->socialite->expects(self::once())->method('driver')
            ->with('azure')
            ->willReturn($this->keycloakProvider);

        $response = $this->keycloakService->revokeToken(self::REFRESH_TOKEN);

        $this->assertArrayHasKey('@odata.context', $response);
        $this->assertArrayHasKey('value', $response);
        $this->assertStringContainsString('graph.keycloak.com', $response['@odata.context']);
        $this->assertIsBool($response['value']);
    }

    /** @covers ::revokeToken */
    public function testRevokeTokenWithAccessToken()
    {
        $this->keycloakProvider->expects(self::once())->method('revoke')
            ->with(self::REFRESH_TOKEN, self::ACCESS_TOKEN)
            ->willReturn([
                '@odata.context' => 'https:\/\/graph.keycloak.com\/v1.0\/$metadata#Edm.Boolean',
                'value' => true,
            ]);
        $this->socialite->expects(self::once())->method('driver')
            ->with('azure')
            ->willReturn($this->keycloakProvider);

        $response = $this->keycloakService->revokeToken(self::REFRESH_TOKEN, self::ACCESS_TOKEN);

        $this->assertArrayHasKey('@odata.context', $response);
        $this->assertArrayHasKey('value', $response);
        $this->assertStringContainsString('graph.keycloak.com', $response['@odata.context']);
        $this->assertIsBool($response['value']);
    }

    /** @covers ::revokeToken */
    public function testRevokeTokenThrowsError()
    {
        $this->keycloakProvider->expects(self::once())->method('revoke')
            ->with(self::REFRESH_TOKEN, self::ACCESS_TOKEN)
            ->willThrowException(new Exception(self::ERROR_MESSAGE));
        $this->socialite->expects(self::once())->method('driver')
            ->with('azure')
            ->willReturn($this->keycloakProvider);

        $this->expectException(Exception::class);
        $this->keycloakService->revokeToken(self::REFRESH_TOKEN, self::ACCESS_TOKEN);
    }
}

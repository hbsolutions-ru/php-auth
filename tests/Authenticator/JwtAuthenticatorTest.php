<?php declare(strict_types=1);

namespace Tests\Authenticator;

use PHPUnit\Framework\TestCase;
use HBS\Auth\Authenticator\JwtAuthenticator;
use HBS\Auth\Exception\AuthenticationException;
use HBS\Auth\Factory\JwtFactory;
use HBS\Auth\Immutable\JwtSettings;

use Tests\AuxiliaryClasses\Identity;
use Tests\AuxiliaryClasses\IdentityToJwtMapper;
use Tests\AuxiliaryClasses\JwtPayloadMapper;

final class JwtAuthenticatorTest extends TestCase
{
    public function testAuthenticateSuccessful(): void
    {
        $userId = 42;

        $settings = new JwtSettings("HS256", 3600, "JwT-\$eCrEt-KeY");

        $factory = new JwtFactory($settings);

        $authenticator = new JwtAuthenticator(
            new JwtPayloadMapper(),
            "test.domain.tld",
            $settings
        );

        $credentials = (new IdentityToJwtMapper($factory))->transform(new Identity($userId));

        try {
            $identity = $authenticator->authenticate($credentials);
        } catch (AuthenticationException $e) {
            $this->fail("Wrong credentials");
        }

        $identityData = $identity->toArray();
        $this->assertArrayHasKey('id', $identityData);
        $this->assertEquals($userId, $identityData['id']);
    }
}

<?php declare(strict_types=1);

namespace Tests\Authenticator;

use PHPUnit\Framework\TestCase;
use HBS\Auth\Authenticator\SecretKeyAuthenticator;
use HBS\Auth\Exception\AuthenticationException;
use HBS\Auth\Model\Credentials\Token;

final class SecretKeyAuthenticatorTest extends TestCase
{
    public function testAuthenticateSuccessful(): void
    {
        $secretKey = "\$eCrEt-KeY";
        $userId = 42;

        $authenticator = new SecretKeyAuthenticator(
            new PayloadMapper(),
            "TEST",
            $secretKey
        );

        $credentials = new Token($secretKey, ['userId' => $userId]);

        try {
            $identity = $authenticator->authenticate($credentials);
        } catch (AuthenticationException $e) {
            $this->fail("Failed to check correct secret key");
        }

        $identityData = $identity->toArray();

        $this->assertArrayHasKey('id', $identityData);
        $this->assertEquals($userId, $identityData['id']);
    }

    public function testAuthenticateWrongCredentials(): void
    {
        $secretKey = "\$eCrEt-KeY";
        $wrongSecretKey = "wrong-secret";
        $userId = 42;

        $authenticator = new SecretKeyAuthenticator(
            new PayloadMapper(),
            "TEST",
            $secretKey
        );

        $credentials = new Token($wrongSecretKey, ['userId' => $userId]);

        try {
            $authenticator->authenticate($credentials);
            $this->fail("Exception not thrown but expected");
        } catch (AuthenticationException $e) {
            $this->assertStringContainsString("Wrong credentials", $e->getMessage());
        }
    }
}

<?php declare(strict_types=1);

namespace Tests\Authenticator;

use PHPUnit\Framework\TestCase;
use HBS\Auth\Authenticator\SecretKeyAuthenticator;
use HBS\Auth\Exception\AuthenticationException;
use HBS\Auth\Model\Credentials\Token;

final class SecretKeyAuthenticatorTest extends TestCase
{
    public function testAuthenticate(): void
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

        $userData = $identity->toArray();

        $this->assertArrayHasKey('id', $userData);
        $this->assertEquals($userId, $userData['id']);
    }
}

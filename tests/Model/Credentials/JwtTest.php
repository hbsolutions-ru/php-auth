<?php declare(strict_types=1);

namespace Tests\Model\Credentials;

use PHPUnit\Framework\TestCase;
use HBS\Auth\Immutable\JwtSettings;
use HBS\Auth\Model\Credentials\Jwt;

final class JwtTest extends TestCase
{
    public function testGetters(): void
    {
        $audience = "client.domain.tld";
        $issuer = "server.domain.tld";
        $subject = "42";
        $ttl = 600;
        $validityDelay = 100;

        $settings = new JwtSettings("HS256", $ttl, "\$eCrEt-KeY");

        $now = time();

        $jwt = new Jwt($settings, [
            Jwt::CLAIM_AUD => $audience,
            Jwt::CLAIM_ISS => $issuer,
            Jwt::CLAIM_NBF => $now + $validityDelay,
            Jwt::CLAIM_SUB => $subject,
        ]);

        $this->assertEquals(
            $audience,
            $jwt->getAudience()
        );

        $this->assertEquals(
            $now + $ttl,
            intval($jwt->getExpirationTime()->format("U"))
        );

        $this->assertEquals(
            $now,
            intval($jwt->getIssuedAt()->format("U"))
        );

        $this->assertEquals(
            $issuer,
            $jwt->getIssuer()
        );

        $this->assertNotEmpty($jwt->getJwtId());

        $this->assertEquals(
            $now + $validityDelay,
            intval($jwt->getNotBefore()->format("U"))
        );

        $this->assertEquals(
            $subject,
            $jwt->getSubject()
        );
    }
}

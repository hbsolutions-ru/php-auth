<?php declare(strict_types=1);

namespace Tests\Mapper;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;
use HBS\Helpers\ObjectHelper;
use PHPUnit\Framework\TestCase;

use HBS\Auth\Factory\JwtFactory;
use HBS\Auth\Immutable\JwtSettings;
use HBS\Auth\Model\Credentials\Jwt;
use HBS\Auth\Model\Credentials\JwtInterface;

use Tests\AuxiliaryClasses\Identity;
use Tests\AuxiliaryClasses\IdentityToJwtMapper;

final class IdentityToJwtMapperTest extends TestCase
{
    public function testTransform(): void
    {
        $algorithm = "HS256";
        $expiration = 3600;
        $secret = "JwT-\$eCrEt-KeY";
        $userId = 42;

        $identity = new Identity($userId);
        $settings = new JwtSettings($algorithm, $expiration, $secret);
        $factory = new JwtFactory($settings);

        $mapper = new IdentityToJwtMapper($factory);

        $now = time();

        $credentials = $mapper->transform($identity);

        /**
         * Check the object
         */
        $this->assertInstanceOf(Jwt::class, $credentials);

        $this->assertEquals($now, $credentials->getIssuedAt()->format("U"));
        $this->assertEquals($now + $expiration, $credentials->getExpirationTime()->format("U"));
        $this->assertEquals($identity->getDomain(), (string)$credentials->getAudience());
        $this->assertEquals($userId, intval($credentials->getSubject()));

        /**
         * Check payload extracted from JWT
         */
        $key = new Key($secret, $algorithm);
        $data = ObjectHelper::toArray(
            FirebaseJWT::decode($credentials->getToken(), $key)
        );

        $this->assertIsArray($data);

        $this->assertArrayHasKey(JwtInterface::CLAIM_IAT, $data);
        $this->assertEquals($now, $data[JwtInterface::CLAIM_IAT]);

        $this->assertArrayHasKey(JwtInterface::CLAIM_EXP, $data);
        $this->assertEquals($now + $expiration, $data[JwtInterface::CLAIM_EXP]);

        $this->assertArrayHasKey(JwtInterface::CLAIM_AUD, $data);
        $this->assertEquals($identity->getDomain(), (string)$data[JwtInterface::CLAIM_AUD]);

        $this->assertArrayHasKey(JwtInterface::CLAIM_SUB, $data);
        $this->assertEquals($userId, intval($data[JwtInterface::CLAIM_SUB]));
    }
}

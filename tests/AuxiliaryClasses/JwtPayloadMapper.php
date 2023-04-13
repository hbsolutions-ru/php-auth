<?php declare(strict_types=1);

namespace Tests\AuxiliaryClasses;

use HBS\Auth\Mapper\ArrayToIdentityInterface;
use HBS\Auth\Model\Credentials\JwtInterface;
use HBS\Auth\Model\Identity\IdentityInterface;

final class JwtPayloadMapper implements ArrayToIdentityInterface
{
    public function transform(array $payload, string $identityDomain): IdentityInterface
    {
        $identity = new Identity(
            intval($payload[JwtInterface::CLAIM_SUB] ?? 0)
        );

        if ($identity->getDomain() !== $identityDomain) {
            throw new \InvalidArgumentException("Domain mismatch");
        }

        return $identity;
    }
}

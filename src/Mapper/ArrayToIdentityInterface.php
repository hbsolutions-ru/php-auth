<?php declare(strict_types=1);

namespace HBS\Auth\Mapper;

use HBS\Auth\Model\Identity\IdentityInterface;

interface ArrayToIdentityInterface
{
    /**
     * @param array $payload
     * @param string $identityDomain
     * @return IdentityInterface
     */
    public function transform(array $payload, string $identityDomain): IdentityInterface;
}

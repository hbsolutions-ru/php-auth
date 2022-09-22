<?php declare(strict_types=1);

namespace Tests\Authenticator;

use HBS\Auth\Mapper\ArrayToIdentityInterface;
use HBS\Auth\Model\Identity\IdentityInterface;
use Tests\AuxiliaryClasses\Identity;

final class PayloadMapper implements ArrayToIdentityInterface
{
    public function transform(array $payload, string $identityDomain): IdentityInterface
    {
        $userId = isset($payload['data']) && is_array($payload['data'])
            ? $payload['data']['id'] ?? 0
            : $payload['userId'] ?? 0;

        return new Identity(
            intval($userId)
        );
    }
}

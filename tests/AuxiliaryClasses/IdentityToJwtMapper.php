<?php declare(strict_types=1);

namespace Tests\AuxiliaryClasses;

use HBS\Auth\Mapper\AbstractIdentityToJwtMapper;
use HBS\Auth\Model\Credentials\JwtInterface;
use HBS\Auth\Model\Identity\IdentityInterface;

final class IdentityToJwtMapper extends AbstractIdentityToJwtMapper
{
    protected function getPayload(IdentityInterface $identity): array
    {
        return [
            JwtInterface::CLAIM_AUD => $identity->getDomain(),
            JwtInterface::CLAIM_SUB => $identity->getId(),
        ];
    }
}

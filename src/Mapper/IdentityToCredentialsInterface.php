<?php declare(strict_types=1);

namespace HBS\Auth\Mapper;

use HBS\Auth\{
    Model\Credentials\CredentialsInterface,
    Model\Identity\IdentityInterface,
};

interface IdentityToCredentialsInterface
{
    /**
     * @param IdentityInterface $identity
     * @param string $credentialsType
     * @return CredentialsInterface
     */
    public function transform(IdentityInterface $identity, string $credentialsType): CredentialsInterface;
}

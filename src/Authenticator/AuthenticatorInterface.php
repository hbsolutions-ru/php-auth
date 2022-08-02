<?php declare(strict_types=1);

namespace HBS\Auth\Authenticator;

use HBS\Auth\{
    Exception\AuthenticationException,
    Model\Credentials\CredentialsInterface,
    Model\Identity\IdentityInterface,
};

interface AuthenticatorInterface
{
    /**
     * Authenticate by credentials
     *
     * @param CredentialsInterface $credentials
     * @return IdentityInterface
     * @throws AuthenticationException
     */
    public function authenticate(CredentialsInterface $credentials): IdentityInterface;
}

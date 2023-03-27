<?php declare(strict_types=1);

namespace HBS\Auth\Authenticator;

use HBS\Helpers\ObjectHelper;
use HBS\Auth\{
    Exception\AuthenticationException,
    Mapper\ArrayToIdentityInterface,
    Model\Credentials\CredentialsInterface,
    Model\Credentials\TokenInterface,
    Model\Identity\IdentityInterface,
};

class SecretKeyAuthenticator implements AuthenticatorInterface
{
    protected ArrayToIdentityInterface $payloadMapper;

    protected string $identityDomain;

    protected string $secretKey;

    public function __construct(
        ArrayToIdentityInterface $payloadMapper,
        string $identityDomain,
        string $secretKey
    ) {
        $this->payloadMapper = $payloadMapper;
        $this->identityDomain = $identityDomain;
        $this->secretKey = $secretKey;
    }

    public function authenticate(CredentialsInterface $credentials): IdentityInterface
    {
        /**
         * Yes, Barbara Liskov won't like it
         * @var $credentials TokenInterface
         */
        if (!ObjectHelper::implementsInterface($credentials, TokenInterface::class)) {
            throw new \InvalidArgumentException(
                \sprintf("The instance must implement the interface %s", TokenInterface::class)
            );
        }

        if ($credentials->getToken() !== $this->secretKey) {
            throw new AuthenticationException("Wrong credentials");
        }

        return $this->payloadMapper->transform($credentials->getPayload(), $this->identityDomain);
    }
}

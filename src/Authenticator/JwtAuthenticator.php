<?php declare(strict_types=1);

namespace HBS\Auth\Authenticator;

use Firebase\JWT\{ExpiredException, JWT, Key};
use HBS\Helpers\ObjectHelper;
use HBS\Auth\{
    Exception\AuthenticationException,
    Immutable\JwtSettings,
    Mapper\ArrayToIdentityInterface,
    Model\Credentials\CredentialsInterface,
    Model\Credentials\TokenInterface,
    Model\Identity\IdentityInterface,
};

class JwtAuthenticator implements AuthenticatorInterface
{
    /**
     * @var ArrayToIdentityInterface
     */
    protected $payloadMapper;

    /**
     * @var string
     */
    protected $identityDomain;

    /**
     * @var JwtSettings
     */
    protected $settings;

    public function __construct(
        ArrayToIdentityInterface $payloadMapper,
        string $identityDomain,
        JwtSettings $settings
    ) {
        $this->payloadMapper = $payloadMapper;
        $this->identityDomain = $identityDomain;
        $this->settings = $settings;
    }

    public function authenticate(CredentialsInterface $credentials): IdentityInterface
    {
        /**
         * Yes, Barbara Liskov won't like it
         * @var $credentials TokenInterface
         */
        if (!ObjectHelper::implementsInterface($credentials, TokenInterface::class)) {
            throw new \InvalidArgumentException(
                sprintf("The instance must implement the interface %s", TokenInterface::class)
            );
        }

        try {
            $key = new Key($this->settings->secret, $this->settings->algorithm);
            $payload = ObjectHelper::toArray(
                JWT::decode($credentials->getToken(), $key)
            );
        } catch (ExpiredException $e) {
            throw new AuthenticationException("Data processing error", $e->getCode(), $e);
        }

        return $this->payloadMapper->transform($payload, $this->identityDomain);
    }
}

<?php declare(strict_types=1);

namespace HBS\Auth\Factory;

use Psr\Log\{LoggerInterface, NullLogger};
use HBS\Auth\{
    Exception\EncryptionException,
    Immutable\HmacSettings,
    Model\Account\AccountInterface,
    Model\Credentials\UsernamePassword,
};

abstract class AbstractAccountFactory
{
    protected HmacSettings $hmacSettings;

    protected string $passwordHashAlgorithm;

    protected LoggerInterface $logger;

    public function __construct(
        HmacSettings $hmacSettings,
        string $passwordHashAlgorithm = PASSWORD_ARGON2ID,
        ?LoggerInterface $logger = null
    ) {
        $this->hmacSettings = $hmacSettings;
        $this->passwordHashAlgorithm = $passwordHashAlgorithm;
        $this->logger = $logger ?? new NullLogger();
    }

    abstract protected function createByUsernameAndPasswordHash(string $username, string $passwordHash): AccountInterface;

    public function create(UsernamePassword $credentials): AccountInterface
    {
        $peppered = \hash_hmac(
            $this->hmacSettings->algorithm,
            $credentials->getPassword(),
            $this->hmacSettings->secret,
        );

        $hash = \password_hash($peppered, $this->passwordHashAlgorithm);

        // Since PHP 8.0: password_hash() no longer returns false on failure, instead a ValueError will be thrown if the password hashing algorithm is not valid, or an Error if the password hashing failed for an unknown error.
        if ($hash === false || $hash === null || !strlen($hash)) {
            $this->logger->error(sprintf(
                "[%s] Failed to create password hash with function: password_hash",
                __CLASS__
            ));
            throw new EncryptionException('Failed to create account');
        }

        return $this->createByUsernameAndPasswordHash($credentials->getUsername(), $hash);
    }
}

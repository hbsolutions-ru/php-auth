<?php declare(strict_types=1);

namespace HBS\Auth\Authenticator;

use Psr\Log\{LoggerInterface, NullLogger};
use HBS\Helpers\ObjectHelper;
use HBS\Auth\{
    Exception\AuthenticationException,
    Immutable\HmacSettings,
    Mapper\AccountEntityToIdentityInterface,
    Model\Credentials\CredentialsInterface,
    Model\Credentials\UsernamePasswordInterface,
    Model\Identity\IdentityInterface,
    Repository\AccountRepositoryInterface,
};

class UsernamePasswordAuthenticator implements AuthenticatorInterface
{
    protected AccountEntityToIdentityInterface $accountMapper;

    protected AccountRepositoryInterface $accountRepository;

    protected string $identityDomain;

    protected LoggerInterface $logger;

    protected HmacSettings $hmacSettings;

    public function __construct(
        AccountEntityToIdentityInterface $accountMapper,
        AccountRepositoryInterface $accountRepository,
        string $identityDomain,
        HmacSettings $hmacSettings,
        ?LoggerInterface $logger = null
    ) {
        $this->accountMapper = $accountMapper;
        $this->accountRepository = $accountRepository;
        $this->identityDomain = $identityDomain;
        $this->hmacSettings = $hmacSettings;
        $this->logger = $logger ?? new NullLogger();
    }

    public function authenticate(CredentialsInterface $credentials): IdentityInterface
    {
        /**
         * Yes, Barbara Liskov won't like it
         * @var $credentials UsernamePasswordInterface
         */
        if (!ObjectHelper::implementsInterface($credentials, UsernamePasswordInterface::class)) {
            throw new \InvalidArgumentException(
                sprintf("The instance must implement the interface %s", UsernamePasswordInterface::class)
            );
        }

        try {
            $account = $this->accountRepository->getByUsername($credentials->getUsername());
        } catch (\RuntimeException $e) {
            // User not found by name or something went wrong
            $this->logger->debug(sprintf(
                "[UsernamePasswordAuthenticator] Failed to retrieve user: %s",
                $e->getMessage()
            ));

            throw new AuthenticationException("Wrong credentials");
        }

        $peppered = hash_hmac($this->hmacSettings->algorithm, $credentials->getPassword(), $this->hmacSettings->secret);

        $success = password_verify($peppered, $account->getPasswordHash());

        if (!$success) {
            throw new AuthenticationException("Wrong credentials");
        }

        return $this->accountMapper->transform($account, $this->identityDomain);
    }
}

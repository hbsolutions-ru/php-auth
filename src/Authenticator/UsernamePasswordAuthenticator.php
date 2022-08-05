<?php declare(strict_types=1);

namespace HBS\Auth\Authenticator;

use HBS\Auth\{
    Exception\AuthenticationException,
    Mapper\AccountEntityToIdentityInterface,
    Model\Credentials\CredentialsInterface,
    Model\Credentials\UsernamePasswordInterface,
    Model\Identity\IdentityInterface,
    Repository\AccountRepositoryInterface,
};

class UsernamePasswordAuthenticator implements AuthenticatorInterface
{
    /**
     * @var AccountEntityToIdentityInterface
     */
    protected $accountMapper;

    /**
     * @var AccountRepositoryInterface
     */
    protected $accountRepository;

    /**
     * @var string
     */
    protected $algorithm;

    /**
     * @var string
     */
    protected $identityDomain;

    /**
     * @var string
     */
    protected $pepper;

    public function __construct(
        AccountEntityToIdentityInterface $accountMapper,
        AccountRepositoryInterface $accountRepository,
        string $algorithm,
        string $identityDomain,
        string $pepper
    ) {
        $this->accountMapper = $accountMapper;
        $this->accountRepository = $accountRepository;
        $this->algorithm = $algorithm;
        $this->identityDomain = $identityDomain;
        $this->pepper = $pepper;
    }

    public function authenticate(CredentialsInterface $credentials): IdentityInterface
    {
        /**
         * Yes, Barbara Liskov won't like it
         * @var $credentials UsernamePasswordInterface
         */
        $reflectionClass = new \ReflectionClass($credentials);
        if (!$reflectionClass->implementsInterface(UsernamePasswordInterface::class)) {
            throw new \InvalidArgumentException(
                sprintf("The instance must implement the interface %s", UsernamePasswordInterface::class)
            );
        }

        $account = $this->accountRepository->getByUsername($credentials->getUsername());

        $peppered = hash_hmac($this->algorithm, $credentials->getPassword(), $this->pepper);

        $success = password_verify($peppered, $account->getPasswordHash());

        if (!$success) {
            throw new AuthenticationException("Wrong credentials");
        }

        return $this->accountMapper->transform($account, $this->identityDomain);
    }
}

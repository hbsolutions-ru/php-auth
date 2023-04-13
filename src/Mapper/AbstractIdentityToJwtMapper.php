<?php declare(strict_types=1);

namespace HBS\Auth\Mapper;

use HBS\Auth\{
    Factory\JwtFactoryInterface,
    Model\Credentials\CredentialsInterface,
    Model\Identity\IdentityInterface,
};

abstract class AbstractIdentityToJwtMapper implements IdentityToCredentialsInterface
{
    protected JwtFactoryInterface $factory;

    public function __construct(JwtFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param IdentityInterface $identity
     * @return CredentialsInterface
     */
    public function transform(IdentityInterface $identity): CredentialsInterface
    {
        return $this->factory->create(
            $this->getPayload($identity)
        );
    }

    /**
     * Transforms Identity to JWT Payload array.
     *
     * @param IdentityInterface $identity
     * @return array
     */
    abstract protected function getPayload(IdentityInterface $identity): array;
}

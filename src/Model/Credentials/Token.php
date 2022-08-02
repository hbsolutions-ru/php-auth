<?php declare(strict_types=1);

namespace HBS\Auth\Model\Credentials;

class Token implements CredentialsInterface
{
    /**
     * @var string
     */
    protected $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}

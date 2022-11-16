<?php declare(strict_types=1);

namespace HBS\Auth\Model\Credentials;

class UsernamePassword implements UsernamePasswordInterface
{
    protected string $username;

    protected string $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}

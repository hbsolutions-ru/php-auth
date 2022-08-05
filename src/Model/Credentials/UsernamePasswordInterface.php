<?php declare(strict_types=1);

namespace HBS\Auth\Model\Credentials;

interface UsernamePasswordInterface extends CredentialsInterface
{
    public function getUsername(): string;

    public function getPassword(): string;
}

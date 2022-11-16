<?php declare(strict_types=1);

namespace HBS\Auth\Model\Credentials;

class Token implements TokenInterface
{
    protected string $token;

    protected array $payload;

    public function __construct(string $token, array $payload = [])
    {
        $this->token = $token;
        $this->payload = $payload;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}

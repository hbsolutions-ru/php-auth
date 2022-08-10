<?php declare(strict_types=1);

namespace HBS\Auth\Immutable;

use HBS\Auth\Exception\AccessViolationException;

/**
 * @property string $algorithm The signing algorithm
 * @property int $expiration JWT expiration time in seconds
 * @property string $secret The secret key
 */
final class JwtSettings
{
    private const PROPERTY_ALGORITHM = 'algorithm';
    private const PROPERTY_EXPIRATION = 'expiration';
    private const PROPERTY_SECRET = 'secret';

    private $data;

    public function __construct(
        string $algorithm,
        int $expiration,
        string $secret
    ) {
        $this->data[self::PROPERTY_ALGORITHM] = $algorithm;
        $this->data[self::PROPERTY_EXPIRATION] = $expiration;
        $this->data[self::PROPERTY_SECRET] = $secret;
    }

    public function __get(string $name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name] ?? null;
        }
        throw new AccessViolationException(sprintf("Property '%s' does not exist", $name));
    }

    public function __set(string $name, $value): void
    {
        throw new AccessViolationException("Object modification is forbidden");
    }

    public function __unset(string $name): void
    {
        throw new AccessViolationException("Object modification is forbidden");
    }
}

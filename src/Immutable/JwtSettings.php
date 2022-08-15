<?php declare(strict_types=1);

namespace HBS\Auth\Immutable;

use HBS\Immutable\AbstractImmutable;

/**
 * @property string $algorithm The signing algorithm
 * @property int $expiration JWT expiration time in seconds
 * @property string $secret The secret key
 */
final class JwtSettings extends AbstractImmutable
{
    private const PROPERTY_ALGORITHM = 'algorithm';
    private const PROPERTY_EXPIRATION = 'expiration';
    private const PROPERTY_SECRET = 'secret';

    public function __construct(
        string $algorithm,
        int $expiration,
        string $secret
    ) {
        $this->data[self::PROPERTY_ALGORITHM] = $algorithm;
        $this->data[self::PROPERTY_EXPIRATION] = $expiration;
        $this->data[self::PROPERTY_SECRET] = $secret;

        parent::__construct();
    }
}

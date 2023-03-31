<?php declare(strict_types=1);

namespace HBS\Auth\Model\Credentials;

use \DateTimeImmutable;

/**
 * @see https://www.rfc-editor.org/rfc/rfc7519
 */
interface JwtInterface extends TokenInterface
{
    public const CLAIM_AUD = 'aud';
    public const CLAIM_EXP = 'exp';
    public const CLAIM_IAT = 'iat';
    public const CLAIM_ISS = 'iss';
    public const CLAIM_JTI = 'jti';
    public const CLAIM_NBF = 'nbf';
    public const CLAIM_SUB = 'sub';

    /**
     * TODO: add return type after 7.4 support is removed
     * RFC allows string|string[]|null
     *
     * @return mixed
     */
    public function getAudience();

    public function getExpirationTime(): ?DateTimeImmutable;

    public function getIssuedAt(): ?DateTimeImmutable;

    public function getIssuer(): ?string;

    public function getJwtId(): ?string;

    public function getNotBefore(): ?DateTimeImmutable;

    public function getSubject(): ?string;
}

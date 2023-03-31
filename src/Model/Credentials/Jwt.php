<?php declare(strict_types=1);

namespace HBS\Auth\Model\Credentials;

use \DateTimeImmutable;

use Firebase\JWT\JWT as FirebaseJWT;
use HBS\Helpers\{
    DateTimeHelper,
    StringHelper,
};
use HBS\Auth\Immutable\JwtSettings;

class Jwt extends Token implements JwtInterface
{
    public function __construct(JwtSettings $settings, array $payload = [])
    {
        // JWT ID
        $jti = StringHelper::randomBase62(16);

        // Issued At
        $iat = DateTimeHelper::now()->getTimestamp();

        // Expiration Time
        $exp = DateTimeImmutable::createFromFormat('U', (string)($iat + $settings->expiration))->getTimestamp();

        $payload = array_merge($payload, [
            self::CLAIM_EXP => $exp,
            self::CLAIM_IAT => $iat,
            self::CLAIM_JTI => $jti,
        ]);

        parent::__construct(
            FirebaseJWT::encode($payload, $settings->secret, $settings->algorithm),
            $payload
        );
    }

    /**
     * TODO: add return type after 7.4 support is removed
     * @return mixed
     */
    public function getAudience()
    {
        return $this->getPayload()[self::CLAIM_AUD] ?? null;
    }

    public function getExpirationTime(): ?DateTimeImmutable
    {
        return $this->getDateTimeClaim(self::CLAIM_EXP);
    }

    public function getIssuedAt(): ?DateTimeImmutable
    {
        return $this->getDateTimeClaim(self::CLAIM_IAT);
    }

    public function getIssuer(): ?string
    {
        return $this->getStringClaim(self::CLAIM_ISS);
    }

    public function getJwtId(): ?string
    {
        return $this->getStringClaim(self::CLAIM_JTI);
    }

    public function getNotBefore(): ?DateTimeImmutable
    {
        return $this->getDateTimeClaim(self::CLAIM_NBF);
    }

    public function getSubject(): ?string
    {
        return $this->getStringClaim(self::CLAIM_SUB);
    }

    protected function getDateTimeClaim(string $claim): ?DateTimeImmutable
    {
        return isset($this->getPayload()[$claim])
            ? DateTimeHelper::timestampToDateTime($this->getPayload()[$claim])
            : null;
    }

    protected function getStringClaim(string $claim): ?string
    {
        return isset($this->getPayload()[$claim])
            ? (string)$this->getPayload()[$claim]
            : null;
    }
}

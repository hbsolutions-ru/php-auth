<?php declare(strict_types=1);

namespace HBS\Auth\Mapper;

use DateTime;
use Firebase\JWT\JWT;
use HBS\Helpers\{
    DateTimeHelper,
    StringHelper,
};
use HBS\Auth\{
    Immutable\JwtSettings,
    Model\Credentials\CredentialsInterface,
    Model\Credentials\Token,
    Model\Identity\IdentityInterface,
};

class IdentityToJwtMapper implements IdentityToCredentialsInterface
{
    /**
     * @var JwtSettings
     */
    protected $settings;

    public function __construct(JwtSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param IdentityInterface $identity
     * @return CredentialsInterface
     * @throws \Exception
     */
    public function transform(IdentityInterface $identity): CredentialsInterface
    {
        // JWT ID
        $jti = StringHelper::randomBase62(16);

        // Issued At
        $iat = DateTimeHelper::now()->getTimestamp();

        // Expiration Time
        $exp = (new DateTime('@' . ($iat + $this->settings->expiration)))->getTimestamp();

        $payload = [
            'jti' => $jti,
            'iat' => $iat,
            'exp' => $exp,
            'data' => $this->getUserData($identity),
        ];

        return new Token(
            JWT::encode($payload, $this->settings->secret, $this->settings->algorithm),
            $payload
        );
    }

    /**
     * Transforms Identity to array with User Data.
     * The method could be overridden in child class implementing some custom logic.
     *
     * @param IdentityInterface $identity
     * @return array
     */
    protected function getUserData(IdentityInterface $identity): array
    {
        return $identity->toArray();
    }
}

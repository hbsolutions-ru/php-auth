<?php declare(strict_types=1);

namespace HBS\Auth\Service;

use Psr\Http\Message\{
    ResponseInterface as Response,
    ResponseFactoryInterface,
    ServerRequestInterface as Request,
};
use Psr\Log\{
    LoggerInterface,
    NullLogger,
};
use HBS\Auth\{
    Authenticator\AuthenticatorInterface,
    Authorizer\AuthorizerInterface,
    Exception\AuthenticationException,
    Model\Credentials\CredentialsInterface,
    Model\Credentials\UsernamePassword,
};

class BasicAuthorizationService implements WebAuthorizationServiceInterface
{
    protected AuthenticatorInterface $authenticator;

    protected AuthorizerInterface $authorizer;

    protected ResponseFactoryInterface $factory;

    protected LoggerInterface $logger;

    public function __construct(
        AuthenticatorInterface $authenticator,
        AuthorizerInterface $authorizer,
        ResponseFactoryInterface $factory,
        LoggerInterface $logger = null
    ) {
        $this->authenticator = $authenticator;
        $this->authorizer = $authorizer;
        $this->factory = $factory;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * Authenticate by the HTTP Authorization header, form (POST) or query (GET) params; and authorize user
     *
     * @param Request $request
     * @return CredentialsInterface|null
     * @throws AuthenticationException
     */
    public function authorize(Request $request): ?CredentialsInterface
    {
        $credentials = $this->getCredentials($request);

        try {
            $identity = $this->authenticator->authenticate($credentials);
        } catch (\RuntimeException $e) {
            throw new AuthenticationException($e->getMessage());
        }

        $this->authorizer->authorize($identity);

        return null;
    }

    protected function getCredentials(Request $request): UsernamePassword
    {
        if (!preg_match("/Basic\s+(.*)$/i", $request->getHeaderLine("Authorization"), $matches)) {
            throw new AuthenticationException('Authentication data not found or is not in the correct format');
        }

        $decodedMatch = base64_decode((string)$matches[1], true);

        if ($decodedMatch === false) {
            throw new AuthenticationException('Authentication data not found or is not in the correct format');
        }

        $credentials = explode(":", $decodedMatch, 2);

        if (!count($credentials) == 2) {
            throw new AuthenticationException('Authentication data not found or is not in the correct format');
        }

        $username = $credentials[0] ?? '';
        $password = $credentials[1] ?? '';

        if (empty($username) || empty($password)) {
            throw new AuthenticationException('Authentication data not found or is not in the correct format');
        }

        return new UsernamePassword($username, $password);
    }

    /**
     * Response for unauthorized user
     *
     * @param AuthenticationException|null $exception
     * @return Response
     */
    public function unauthorized(?AuthenticationException $exception = null): Response
    {
        $this->logger->notice(sprintf(
            "[%s] Authentication failed: %s",
            __CLASS__,
            $exception ? $exception->getMessage() : "Unknown error"
        ));

        $response = $this->factory->createResponse();

        return $response
            ->withHeader('WWW-Authenticate', 'Basic realm="Access denied"')
            ->withStatus(401);
    }
}

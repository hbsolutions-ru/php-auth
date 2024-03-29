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
    Model\Credentials\Token,
};

class BearerAuthorizationService implements WebAuthorizationServiceInterface
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
        $bearer = $this->getBearer($request);

        try {
            $identity = $this->authenticator->authenticate($bearer);
        } catch (\RuntimeException $e) {
            throw new AuthenticationException($e->getMessage());
        }

        $this->authorizer->authorize($identity);

        return null;
    }

    protected function getBearer(Request $request): Token
    {
        $bearer = null;

        // Try authorization header
        if (\preg_match("/Bearer\s+(.*)$/i", $request->getHeaderLine("Authorization"), $matches)) {
            $bearer = (string)$matches[1];
        }

        if (empty($bearer)) {
            $errorMessage = "Authentication data not found or is not in the correct format";

            $this->logger->debug(\sprintf("[%s] %s", __CLASS__, $errorMessage));
            throw new AuthenticationException($errorMessage);
        }

        return new Token($bearer);
    }

    /**
     * Response for unauthorized user
     *
     * @param AuthenticationException|null $exception
     * @return Response
     */
    public function unauthorized(?AuthenticationException $exception = null): Response
    {
        $this->logger->notice(\sprintf(
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

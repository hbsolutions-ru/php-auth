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

class QueryParamAuthorizationService implements WebAuthorizationServiceInterface
{
    protected AuthenticatorInterface $authenticator;

    protected AuthorizerInterface $authorizer;

    protected ResponseFactoryInterface $factory;

    protected LoggerInterface $logger;

    protected string $queryParamName;

    public function __construct(
        AuthenticatorInterface $authenticator,
        AuthorizerInterface $authorizer,
        ResponseFactoryInterface $factory,
        string $queryParamName,
        LoggerInterface $logger = null
    ) {
        $this->authenticator = $authenticator;
        $this->authorizer = $authorizer;
        $this->factory = $factory;
        $this->queryParamName = $queryParamName;
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
        $token = $this->getToken($request);

        try {
            $identity = $this->authenticator->authenticate($token);
        } catch (\RuntimeException $e) {
            throw new AuthenticationException($e->getMessage());
        }

        $this->authorizer->authorize($identity);

        return null;
    }

    protected function getToken(Request $request): Token
    {
        $token = (string)$request->getQueryParams()[$this->queryParamName] ?? null;

        if (empty($token)) {
            throw new AuthenticationException('Authentication data not found or is not in the correct format');
        }

        return new Token($token);
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

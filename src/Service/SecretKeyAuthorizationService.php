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
    Mapper\IdentityToCredentialsInterface,
    Model\Credentials\CredentialsInterface,
    Model\Credentials\Token,
};

class SecretKeyAuthorizationService implements WebAuthorizationServiceInterface
{
    protected AuthenticatorInterface $authenticator;

    protected AuthorizerInterface $authorizer;

    protected ResponseFactoryInterface $factory;

    protected string $paramName;

    protected ?IdentityToCredentialsInterface $passThroughAuthMapper;

    protected LoggerInterface $logger;

    public function __construct(
        AuthenticatorInterface $authenticator,
        AuthorizerInterface $authorizer,
        ResponseFactoryInterface $factory,
        string $paramName = 'token',
        IdentityToCredentialsInterface $passThroughAuthMapper = null,
        LoggerInterface $logger = null
    ) {
        $this->authenticator = $authenticator;
        $this->authorizer = $authorizer;
        $this->factory = $factory;
        $this->paramName = $paramName;
        $this->passThroughAuthMapper = $passThroughAuthMapper;
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

        if ($this->passThroughAuthMapper === null) {
            return null;
        }

        return $this->passThroughAuthMapper->transform($identity);
    }

    protected function getCredentials(Request $request): Token
    {
        $bodyParams = (array)$request->getParsedBody();

        $token = $bodyParams[$this->paramName] ?? '';

        if (empty($token)) {
            throw new AuthenticationException('Authentication data not found or is not in the correct format');
        }

        unset($bodyParams[$this->paramName]);

        return new Token($token, $bodyParams);
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

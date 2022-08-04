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
    Model\Credentials\UsernamePassword,
};

class RequestBodyAuthorizationService implements WebAuthorizationServiceInterface
{
    /**
     * @var AuthenticatorInterface
     */
    protected $authenticator;

    /**
     * @var AuthorizerInterface
     */
    protected $authorizer;

    /**
     * @var ResponseFactoryInterface
     */
    protected $factory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $usernameParamName;

    /**
     * @var string
     */
    protected $passwordParamName;

    public function __construct(
        AuthenticatorInterface $authenticator,
        AuthorizerInterface $authorizer,
        ResponseFactoryInterface $factory,
        string $usernameParamName = 'username',
        string $passwordParamName = 'password',
        LoggerInterface $logger = null
    ) {
        $this->authenticator = $authenticator;
        $this->authorizer = $authorizer;
        $this->factory = $factory;
        $this->usernameParamName = $usernameParamName;
        $this->passwordParamName = $passwordParamName;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * Authenticate (by the HTTP Authorization header or query param) and authorize user
     *
     * @param Request $request
     * @throws AuthenticationException
     */
    public function authorize(Request $request): void
    {
        $credentials = $this->getCredentials($request);

        try {
            $identity = $this->authenticator->authenticate($credentials);
        } catch (\RuntimeException $e) {
            throw new AuthenticationException($e->getMessage());
        }

        $this->authorizer->authorize($identity);
    }

    protected function getCredentials(Request $request): UsernamePassword
    {
        $bodyParams = (array)$request->getParsedBody();

        $username = $bodyParams[$this->usernameParamName] ?? '';
        $password = $bodyParams[$this->passwordParamName] ?? '';

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

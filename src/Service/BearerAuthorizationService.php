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
    Model\Credentials\Token,
};

class BearerAuthorizationService implements WebAuthorizationServiceInterface
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
     * Authenticate (by the HTTP Authorization header or query param) and authorize user
     *
     * @param Request $request
     * @throws AuthenticationException
     */
    public function authorize(Request $request): void
    {
        $bearer = $this->getBearer($request);

        try {
            $identity = $this->authenticator->authenticate($bearer);
        } catch (\RuntimeException $e) {
            throw new AuthenticationException($e->getMessage());
        }

        $this->authorizer->authorize($identity);
    }

    protected function getBearer(Request $request): Token
    {
        $bearer = null;

        // Try authorization header
        if (preg_match("/Bearer\s+(.*)$/i", $request->getHeaderLine("Authorization"), $matches)) {
            $bearer = (string)$matches[1];
        }

        if (empty($bearer)) {
            throw new AuthenticationException('Authentication data not found or is not in the correct format');
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

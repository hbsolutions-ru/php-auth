<?php declare(strict_types=1);

namespace HBS\Auth\Service;

use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request,
};
use HBS\Auth\{
    Exception\AuthenticationException,
    Model\Credentials\CredentialsInterface,
};

interface WebAuthorizationServiceInterface
{
    /**
     * Authenticate by the HTTP Authorization header, form (POST) or query (GET) params; and authorize user
     *
     * @param Request $request
     * @return CredentialsInterface|null
     * @throws AuthenticationException
     */
    public function authorize(Request $request): ?CredentialsInterface;

    /**
     * Response for unauthorized user
     *
     * @param AuthenticationException|null $exception
     * @return Response
     */
    public function unauthorized(?AuthenticationException $exception = null): Response;
}

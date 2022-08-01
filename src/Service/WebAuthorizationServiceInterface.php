<?php declare(strict_types=1);

namespace HBS\Auth\Service;

use Psr\Http\Message\{
    ResponseInterface as Response,
    ServerRequestInterface as Request,
};
use HBS\Auth\Exception\AuthenticationException;

interface WebAuthorizationServiceInterface
{
    /**
     * Authenticate (by the HTTP Authorization header or query param) and authorize user
     *
     * @param Request $request
     * @throws AuthenticationException
     */
    public function authorize(Request $request): void;

    /**
     * Response for unauthorized user
     *
     * @param AuthenticationException|null $exception
     * @return Response
     */
    public function unauthorized(?AuthenticationException $exception = null): Response;
}

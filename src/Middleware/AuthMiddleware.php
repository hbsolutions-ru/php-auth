<?php declare(strict_types=1);

namespace HBS\Auth\Middleware;

use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};
use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface,
};
use HBS\Auth\Exception\AuthenticationException;
use HBS\Auth\Service\WebAuthorizationServiceInterface;

class AuthMiddleware implements MiddlewareInterface
{
    protected WebAuthorizationServiceInterface $service;

    public function __construct(WebAuthorizationServiceInterface $service)
    {
        $this->service = $service;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $this->service->authorize($request);
        } catch (AuthenticationException $exception) {
            return $this->service->unauthorized($exception);
        }

        return $handler->handle($request);
    }
}

<?php

namespace TacraftSociality\HtmlErrors;

use Flarum\Http\Exception\RouteNotFoundException;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RedirectErrorMiddleware implements MiddlewareInterface
{
    private const TARGET = 'https://www.tcscraft.com/tcs/error';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        if (str_starts_with($path, '/api')
            || str_starts_with($path, '/admin')
            || str_starts_with($path, '/auth')
            || str_starts_with($path, '/microsoft-oauth')
            || str_starts_with($path, '/github-oauth')
            || str_starts_with($path, '/blessing-oauth')
        ) {
            return $handler->handle($request);
        }

        if ($path === parse_url(self::TARGET, PHP_URL_PATH)) {
            return $handler->handle($request);
        }

        try {
            $response = $handler->handle($request);
        } catch (RouteNotFoundException $e) {
            return new RedirectResponse(self::TARGET, 302);
        }

        $status = $response->getStatusCode();

        if ($status === 404 || ($status >= 500 && $status < 600)) {
            return new RedirectResponse(self::TARGET, 302);
        }

        return $response;
    }
}
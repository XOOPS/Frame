<?php

namespace Xoops\Frame\Panel;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class Blank
 *
 * A blank (dummy placeholder) panel
 *
 * @package Xoops\Frame\Panel
 *
 */
class Blank implements MiddlewareInterface
{

    /**
     * Process an incoming server request by delegating the response creation to a handler.
     *
     * @param ServerRequestInterface $request an Http Request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        return $response;
    }
}

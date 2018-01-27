<?php

namespace Xoops\Frame\Panel;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Closure;

class ClosureToMiddleware implements MiddlewareInterface
{

    /** @var Closure $closure */
    protected $closure;

    /**
     * ClosureToMiddleware constructor.
     *
     * @param Closure $closure
     */
    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * Here, we will use the Closure set at instantiation, passing it ($request, $handler)
     *
     * @param ServerRequestInterface $request an Http Request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $closure = $this->closure;
        $response = $closure($request, $handler);
        // we cannot guarantee the closure follows the rules and returns a response
        // if it doesn't, delegate to the passed handler
        if (!($response instanceof ResponseInterface)) {
            $response = $handler->handle($request);
        }
        return $response;
    }
}

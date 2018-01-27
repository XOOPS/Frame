<?php

namespace Xoops\Frame;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Xoops\Frame\Exception\InvalidHandlerException;
use Xoops\Frame\Exception\RackExhaustedException;

/**
 * Class Rack
 *
 * @package Xoops\Xadr
 */
class Rack implements MiddlewareInterface, RequestHandlerInterface
{
    /** @var \SplQueue  */
    protected $workQueue;

    /**
     * Rack constructor.
     */
    public function __construct()
    {
        $this->workQueue = new \SplQueue();
    }

    /**
     * Add ServerMiddleware to the queue
     *
     * @param MiddlewareInterface $middleware
     *
     * @return self
     *
     * @throws InvalidHandlerException
     */
    public function add(MiddlewareInterface $middleware): Rack
    {
        if ($this === $middleware) {
            throw new InvalidHandlerException('Cannot register a Rack instance as its own middleware');
        }
        $this->workQueue->enqueue($middleware);
        return $this;
    }

    /**
     * Handle the request and return a response
     *
     * @param ServerRequestInterface $request an Http Request
     *
     * @return ResponseInterface
     *
     * @throws InvalidHandlerException
     * @throws RackExhaustedException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $handler = $this->workQueue->dequeue();
            if (!($handler instanceof MiddlewareInterface)) {
                throw new InvalidHandlerException('Invalid Handler in Rack queue');
            }
            return $handler->process($request, $this);
        } catch (\RuntimeException $e) {
            throw new RackExhaustedException('Rack exhausted', 0, $e);
        }
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * Here, we will use our own workQueue to try to handle the request, allowing stacking of
     * Rack instances in a middleware queue (i.e a Rack of Racks)
     *
     * @param ServerRequestInterface $request an Http Request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     *
     * @throws InvalidHandlerException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $this->handle($request);
        } catch (RackExhaustedException $e) {
            // this rack didn't handle it, delegate it
            return $handler->handle($request);
        }
    }

    /**
     * Process a request using the middleware that has been add()ed to the Rack
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws InvalidHandlerException
     * @throws RackExhaustedException
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        return $this->handle($request);
    }

    /**
     * __invoke() is an alias for run()
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws InvalidHandlerException
     * @throws RackExhaustedException
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return $this->run($request);
    }
}

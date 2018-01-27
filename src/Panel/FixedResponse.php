<?php

namespace Xoops\Frame\Panel;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class FixedResponse
 *
 * Return a fixed Response set at instantiation
 *
 * @package Xoops\Frame\Panel
 *
 */
class FixedResponse implements MiddlewareInterface
{
    /** @var ResponseInterface $response */
    protected $response;

    /**
     * FixedResponse constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }


    /**
     * Process an incoming server request by returning a preset response
     *
     * @param ServerRequestInterface $request an Http Request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->response;
    }
}

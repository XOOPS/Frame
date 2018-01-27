<?php

namespace Xoops\Test\Frame\Panel;

use Xoops\Frame\Panel\ClosureToMiddleware;

class ClosureToMiddlewareTest extends \PHPUnit\Framework\TestCase
{
    /** @var FixedResponse */
    protected $object;

    protected $response;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->response = $this->generateMockResponseInterface();
        $this->object = new ClosureToMiddleware(\Closure::fromCallable([$this, 'mockResponse']));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * generate a MiddlewareInterface object that returns the response passed to its constructor
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Server\MiddlewareInterface
     */
    protected function mockResponse(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Server\RequestHandlerInterface $handler): \Psr\Http\Message\ResponseInterface
    {
        return $this->response;
    }

    /**
     * generate an empty ResponseInterface object
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function generateMockRequestHandlerInterface()
    {
        return $this->createMock('\Psr\Http\Server\RequestHandlerInterface');
    }

    /**
     * generate an empty ResponseInterface object
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function generateMockResponseInterface()
    {
        return $this->createMock('\Psr\Http\Message\ResponseInterface');
    }

    /**
     * generate an empty ServerRequestInterface object
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function generateMockServerRequestInterface()
    {
        return $this->createMock('\Psr\Http\Message\ServerRequestInterface');
    }

    public function testContracts()
    {
        $this->assertInstanceOf('\Xoops\Frame\Panel\ClosureToMiddleware', $this->object);
        $this->assertInstanceOf('\Psr\Http\Server\MiddlewareInterface', $this->object);
    }

    public function testProcess()
    {
        $request  = $this->generateMockServerRequestInterface();
        $handler  = $this->generateMockRequestHandlerInterface();
        $actual = $this->object->process($request, $handler);
        $this->assertSame($this->response, $actual);
    }
}

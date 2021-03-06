<?php

namespace Xoops\Test\Frame\Panel;

use Xoops\Frame\Panel\FixedResponse;

class FixedResponseTest extends \PHPUnit\Framework\TestCase
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
        $this->object = new FixedResponse($this->response);
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
    protected function generateMockHandler(\Psr\Http\Message\ResponseInterface $response)
    {
        $handler = new class() implements \Psr\Http\Server\RequestHandlerInterface {
            public function handle(\Psr\Http\Message\ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface
            {
                throw new \RuntimeException('This mock handler should never be called!!');
            }
        };
        return $handler;
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
        $this->assertInstanceOf('\Xoops\Frame\Panel\FixedResponse', $this->object);
        $this->assertInstanceOf('\Psr\Http\Server\MiddlewareInterface', $this->object);
    }

    public function testProcess()
    {
        $request  = $this->generateMockServerRequestInterface();
        $response = $this->generateMockResponseInterface();
        $handler  = $this->generateMockHandler($response);
        $actual = $this->object->process($request, $handler);
        $this->assertSame($this->response, $actual);
    }
}

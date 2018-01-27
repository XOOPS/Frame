<?php

namespace Xoops\Test\Frame\Panel;

use Xoops\Frame\Panel\Blank;

class BlankTest extends \PHPUnit\Framework\TestCase
{
    /** @var Blank */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Blank();
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
        $handler = new class($response) implements \Psr\Http\Server\RequestHandlerInterface {
            private $response;

            public function __construct($response)
            {
                $this->response = $response;
            }
            public function handle(\Psr\Http\Message\ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface
            {
                return $this->response;
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
        $this->assertInstanceOf('\Xoops\Frame\Panel\Blank', $this->object);
        $this->assertInstanceOf('\Psr\Http\Server\MiddlewareInterface', $this->object);
    }

    public function testProcess()
    {
        $request  = $this->generateMockServerRequestInterface();
        $response = $this->generateMockResponseInterface();
        $handler  = $this->generateMockHandler($response);
        $actual = $this->object->process($request, $handler);
        $this->assertSame($response, $actual);
    }
}

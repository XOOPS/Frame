<?php

namespace Xoops\Test\Frame;

use Xoops\Frame\Rack;

class RackTest extends \PHPUnit\Framework\TestCase
{
    /** @var Rack */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Rack();
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
    protected function generateMockMiddleware(\Psr\Http\Message\ResponseInterface $response)
    {
        $middleware = new class($response) implements \Psr\Http\Server\MiddlewareInterface {
            private $response;
            public $wasRun = null;

            public function __construct($response)
            {
                $this->response = $response;
            }
            public function process(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Server\RequestHandlerInterface $handler): \Psr\Http\Message\ResponseInterface
            {
                $this->wasRun = $this;
                return $this->response;
            }
        };
        return $middleware;
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
        $this->assertInstanceOf('\Xoops\Frame\Rack', $this->object);
        $this->assertInstanceOf('\Psr\Http\Server\MiddlewareInterface', $this->object);
        $this->assertInstanceOf('\Psr\Http\Server\RequestHandlerInterface', $this->object);
        $rack1 = new Rack();
        $rack2 = new Rack();
        $this->assertNotSame($rack1, $rack2);
    }

    public function testAdd()
    {
        $request = $this->generateMockServerRequestInterface();
        $response = $this->generateMockResponseInterface();
        $middleware = $this->generateMockMiddleware($response);
        $this->object->add($middleware);
        $this->object->run($request);
        $actual = $middleware->wasRun;
        $this->assertSame($middleware, $actual);  // THE same middleware instance we added was run

        /*
        $middleware = $this->createMock('\Psr\Http\Server\MiddlewareInterface');
        $this->object->add($middleware);
        $closure = function () { return($this->workQueue->top()); };
        $top = $closure->call($this->object);
        $this->assertSame($middleware, $top);
        */
    }

    public function testAdd_InvalidHandlerException()
    {
        $this->expectException('\Xoops\Frame\Exception\InvalidHandlerException');
        $this->object->add($this->object);
    }

    public function testHandle()
    {
        $request = $this->generateMockServerRequestInterface();
        $response = $this->generateMockResponseInterface();
        $middleware = $this->generateMockMiddleware($response);
        $this->object->add($middleware);
        $actual = $this->object->handle($request);
        $this->assertSame($response, $actual);
    }

    public function testProcess()
    {
        $request = $this->generateMockServerRequestInterface();
        $response = $this->generateMockResponseInterface();
        $middleware = $this->generateMockMiddleware($response);
        $this->object->add($middleware);
        $actual = $this->object->process($request, $this->object);
        $this->assertSame($response, $actual);
    }

    public function testRun()
    {
        $request = $this->generateMockServerRequestInterface();
        $response = $this->generateMockResponseInterface();
        $middleware = $this->generateMockMiddleware($response);
        $this->object->add($middleware);
        $actual = $this->object->run($request);
        $this->assertSame($response, $actual);
    }

    public function testRun_RackExhaustedException()
    {
        $request = $this->generateMockServerRequestInterface();
        $this->expectException('\Xoops\Frame\Exception\RackExhaustedException');
        $actual = $this->object->run($request);
    }

    public function testRun_InvalidHandlerException()
    {
        // mess with internals to force a bad handler into the queue
        $closure = function () { return($this->workQueue); };
        /** @var \SplQueue $queue */
        $queue = $closure->call($this->object);
        $queue->enqueue(new \stdClass());
        $request = $this->generateMockServerRequestInterface();
        $this->expectException('\Xoops\Frame\Exception\InvalidHandlerException');
        $actual = $this->object->run($request);
    }

    public function testStackedRacks()
    {
        $rack1 = new Rack();
        $rack2 = new Rack();
        $request = $this->generateMockServerRequestInterface();
        $response = $this->generateMockResponseInterface();
        $middleware = $this->generateMockMiddleware($response);
        $rack1->add($rack2);
        $rack2->add($middleware);
        $actual = $rack1->run($request);
        $this->assertSame($response, $actual);
    }

    public function testEmptyStackedRacks()
    {
        $rack1 = new Rack();
        $rack2 = new Rack();
        $request = $this->generateMockServerRequestInterface();
        $response = $this->generateMockResponseInterface();
        $middleware = $this->generateMockMiddleware($response);
        $this->object->add($rack1);
        $this->object->add($rack2);
        $this->object->add($middleware);
        $actual = $this->object->run($request);
        $this->assertSame($response, $actual);
    }

    public function testEmptyStackedRacks2()
    {
        $rack1 = new Rack();
        $rack2 = new Rack();
        $request = $this->generateMockServerRequestInterface();
        $this->object->add($rack1);
        $this->object->add($rack2);
        $this->expectException('\Xoops\Frame\Exception\RackExhaustedException');
        $actual = $this->object->run($request);
    }

    public function test__invoke()
    {
        $request = $this->generateMockServerRequestInterface();
        $response = $this->generateMockResponseInterface();
        $middleware = $this->generateMockMiddleware($response);
        $this->object->add($middleware);
        $actual = ($this->object)($request);
        $this->assertSame($response, $actual);
    }

    public function test__invoke_RackExhaustedException()
    {
        $request = $this->generateMockServerRequestInterface();
        $this->expectException('\Xoops\Frame\Exception\RackExhaustedException');
        $actual = ($this->object)($request);
    }

    public function test__invoke_InvalidHandlerException()
    {
        // mess with internals to force a bad handler into the queue
        $closure = function () { return($this->workQueue); };
        /** @var \SplQueue $queue */
        $queue = $closure->call($this->object);
        $queue->enqueue(new \stdClass());
        $request = $this->generateMockServerRequestInterface();
        $this->expectException('\Xoops\Frame\Exception\InvalidHandlerException');
        $actual = ($this->object)($request);
    }
}
<?php
namespace Xoops\Test\Frame\Exception;

use Xoops\Frame\Exception\InvalidHandlerException;

class InvalidHandlerExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var InvalidHandlerException
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new InvalidHandlerException;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testContracts()
    {
        $this->assertInstanceOf('\Xoops\Frame\Exception\InvalidHandlerException', $this->object);
        $this->assertInstanceOf('\InvalidArgumentException', $this->object);
    }

    public function testException()
    {
        $this->expectException('\Xoops\Frame\Exception\InvalidHandlerException');
        throw $this->object;
    }
}

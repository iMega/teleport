<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Dmitry Gavrilov <info@imega.ru>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace iMega;

use Gaufrette;
use \PHPUnit_Framework_MockObject_MockObject;

/**
 * Class PackerSubscriber204Test
 */
class PackerSubscriber204Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $data;

    /**
     * setUp
     */
    public function setUp()
    {
        $adapter = new Gaufrette\Adapter\Local(__DIR__.'/../../../Fixtures/2.04/expected');
        $fs = new Gaufrette\Filesystem($adapter);
        $this->data = $fs->read('import.txt');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $buffer = new \iMega\Teleport\Buffers\Memory();

        $mapperMock = $this->getMockBuilder('iMega\Teleport\Mapper\Mysqlnd')
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMock();

        $actual = new \iMega\Teleport\Subscriber\PackerSubscriber($buffer, $mapperMock, 999999);
        $this->assertInstanceOf('\\iMega\\Teleport\\Subscriber\\PackerSubscriber', $actual);
    }

    /**
     * @test
     */
    public function testPackStock()
    {
        $buffer = new \iMega\Teleport\Buffers\Memory();

        $actual = '';
        $mapperMock = $this->getMockBuilder('iMega\Teleport\Mapper\Mysqlnd')
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMock();
        $mapperMock->expects($this->any())
            ->method('execute')
            ->will(
                $this->returnCallback(function ($key, $data) use (&$actual) {
                    $actual = $data;
                })
            );

        $bufferSubscriber = new \iMega\Teleport\Subscriber\BufferSubscriber($buffer);
        $packerSubscriber = new \iMega\Teleport\Subscriber\PackerSubscriber($buffer, $mapperMock, 0);

        $data = explode("\n", $this->data);
        foreach ($data as $line) {
            if (!$line) {
                continue;
            }
            $eventParseStock = new \iMega\Teleport\Events\ParseStock(json_decode($line, true));
            $bufferSubscriber->parseStock($eventParseStock);
            $packerSubscriber->packStock($eventParseStock);

            $this->assertEquals($line, $actual[0]);
        }
    }

    /**
     * @test
     */
    public function testPackStockEnd()
    {
        $buffer = new \iMega\Teleport\Buffers\Memory();

        $actual = [];
        $mapperMock = $this->getMockBuilder('iMega\Teleport\Mapper\Mysqlnd')
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMock();
        $mapperMock->expects($this->any())
            ->method('execute')
            ->will(
                $this->returnCallback(function ($key, $data) use (&$actual) {
                    $actual = array_merge($actual, $data);
                })
            );

        $bufferSubscriber = new \iMega\Teleport\Subscriber\BufferSubscriber($buffer);
        $packerSubscriber = new \iMega\Teleport\Subscriber\PackerSubscriber($buffer, $mapperMock, 999999);

        $data = explode("\n", $this->data);
        foreach ($data as $line) {
            if (!$line) {
                continue;
            }
            $eventParseStock = new \iMega\Teleport\Events\ParseStock(json_decode($line, true));
            $bufferSubscriber->parseStock($eventParseStock);
            $packerSubscriber->packStock($eventParseStock);
        }
        $packerSubscriber->packStockEnd();

        //Add empty array as newline
        array_push($actual, '');

        $this->assertEquals(sort($data), sort($actual));
    }
}

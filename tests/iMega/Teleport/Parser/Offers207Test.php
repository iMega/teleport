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
 * Class StockTest
 */
class Offers207Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $data;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dispatcher;
    /**
     * setUp
     */
    public function setUp()
    {
        $adapter = new Gaufrette\Adapter\Local(__DIR__.'/../../../Fixtures/2.07');
        $fs = new Gaufrette\Filesystem($adapter);
        $this->data = $fs->read('offers.xml');
        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->dispatcher->expects($this->any())
            ->method('dispatch');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $actual = new \iMega\Teleport\Parser\Offers($this->data, $this->dispatcher);
        $this->assertInstanceOf('\\iMega\\Teleport\\Parser\\Offers', $actual);
    }

    /**
     * @test
     */
    public function testParse()
    {
        $actual = '';
        $offersMock = $this->getMockBuilder('iMega\Teleport\Parser\Offers')
            ->setConstructorArgs(array($this->data, $this->dispatcher))
            ->setMethods(['event'])
            ->getMock();
        $offersMock->expects($this->any())
            ->method('event')
            ->will(
                $this->returnCallback(function($data) use (&$actual) {
                    $actual .= json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
                })
            );

        $offersMock->parse();

        $adapter = new Gaufrette\Adapter\Local(__DIR__.'/../../../Fixtures/2.07/expected');
        $fs = new Gaufrette\Filesystem($adapter);
        $expected = $fs->read('offers.txt');

        $this->assertEquals($expected, $actual);
    }
}

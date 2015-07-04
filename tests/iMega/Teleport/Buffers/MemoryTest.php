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
 * Class MemoryTest
 */
class MemoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $data;

    /**
     * @var \iMega\Teleport\BufferInterface
     */
    protected $buffer;

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
        $actual = new \iMega\Teleport\Buffers\Memory();
        $this->assertInstanceOf('\\iMega\\Teleport\\Buffers\\Memory', $actual);
    }
}

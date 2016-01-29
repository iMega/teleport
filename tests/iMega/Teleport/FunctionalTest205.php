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
 * Class FunctionalTest
 */
class FunctionalTest205 extends \PHPUnit_Framework_TestCase
{
    /**
     * @var
     */
    protected $connector;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->connector = curl_init();
        curl_setopt($this->connector, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->connector, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->connector, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->connector, CURLOPT_USERAGENT, '1C+Enterprise/8.2');
        curl_setopt($this->connector, CURLOPT_USERPWD, "demo:111111");
    }

    /**
     * tearDown
     */
    public function tearDown()
    {
        curl_close($this->connector);
    }

    /**
     * @test
     */
    public function testCheckauth()
    {
        curl_setopt($this->connector, CURLOPT_URL, getenv("BASE_URL") . "/1c_exchange.php?type=catalog&mode=checkauth");
        $response = curl_exec($this->connector);

        $this->assertEquals("success\n", $response);
    }

    /**
     * @test
     */
    public function testInit()
    {
        curl_setopt($this->connector, CURLOPT_URL, getenv("BASE_URL") . "/1c_exchange.php?type=catalog&mode=init");
        $response = curl_exec($this->connector);

        $this->assertEquals("zip=no\nfile_limit=2000000\n", $response);
    }

    /**
     * @test
     */
    public function testFileImport()
    {
        $adapter = new Gaufrette\Adapter\Local(__DIR__.'/../../Fixtures/2.05');
        $fs = new Gaufrette\Filesystem($adapter);
        $importFile = $fs->read('import.xml');

        curl_setopt($this->connector, CURLOPT_URL, getenv("BASE_URL") . "/1c_exchange.php?type=catalog&mode=file&filename=import.xml");
        curl_setopt($this->connector, CURLOPT_POSTFIELDS, $importFile);
        curl_setopt($this->connector, CURLOPT_POST, true);
        $response = curl_exec($this->connector);

        $this->assertEquals("success\n", $response);
    }

    /**
     * @test
     */
    public function testFileOffers()
    {
        $adapter = new Gaufrette\Adapter\Local(__DIR__.'/../../Fixtures/2.05');
        $fs = new Gaufrette\Filesystem($adapter);
        $offersFile = $fs->read('offers.xml');

        curl_setopt($this->connector, CURLOPT_URL, getenv("BASE_URL") . "/1c_exchange.php?type=catalog&mode=file&filename=offers.xml");
        curl_setopt($this->connector, CURLOPT_POSTFIELDS, $offersFile);
        curl_setopt($this->connector, CURLOPT_POST, true);
        $response = curl_exec($this->connector);

        $this->assertEquals("success\n", $response);
    }

    /**
     * @test
     */
    public function testImport()
    {
        curl_setopt($this->connector, CURLOPT_URL, getenv("BASE_URL") . "/1c_exchange.php?type=catalog&mode=import&filename=import.xml");
        $response = curl_exec($this->connector);

        $this->assertEquals("success\n", $response);
    }
}

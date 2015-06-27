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
 * Class Mysqlnd204Test
 */
class Mysqlnd204Test extends \PHPUnit_Extensions_Database_TestCase
{
    /**
     * @var string
     */
    protected $data;

    /**
     * @var \mysqli
     */
    protected $mapper;

    public function getConnection()
    {
        $dsn = 'mysql:dbname=teleport;host='.getenv('DB_HOST');
        $pdo = new \PDO($dsn, 'root');
        return $this->createDefaultDBConnection($pdo, $_ENV["DB_NAME"]);
    }

    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__.'/../../../Fixtures/2.04/expected/datasets/import.xml');
    }

    /**
     * setUp
     */
    public function setUp()
    {
        $adapter = new Gaufrette\Adapter\Local(__DIR__.'/../../../Fixtures/2.04/expected');
        $fs = new Gaufrette\Filesystem($adapter);
        $this->data = $fs->read('import.txt');

        $adapter = new Gaufrette\Adapter\Local(__DIR__.'/../../../../build/sql');
        $fs = new Gaufrette\Filesystem($adapter);
        $queries = $fs->read('tabs.sql');

        $this->mapper = new \iMega\Teleport\Mapper\Mysqlnd([
            'host'   => getenv('DB_HOST'),
            'prefix' => $_ENV["DB_PREFIX"],
            'db'     => $_ENV["DB_NAME"],
            'user'   => $_ENV["DB_USER"],
            'pass'   => $_ENV["DB_PASS"],
            'port'   => null,
            'socket' => null
        ]);

        $queries = str_replace('{$table_prefix}', $_ENV["DB_PREFIX"], $queries);

        $this->mapper->query($queries);

        //$this->mysql->close();
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('\\iMega\\Teleport\\Mapper\\Mysqlnd', $this->mapper);
    }

    /**
     * @test
     */
    public function testExecute()
    {
        $mapper = new \iMega\Teleport\Mapper\Mysqlnd([
            'host'   => getenv('DB_HOST'),
            'prefix' => $_ENV["DB_PREFIX"],
            'db'     => $_ENV["DB_NAME"],
            'user'   => $_ENV["DB_USER"],
            'pass'   => $_ENV["DB_PASS"],
            'port'   => null,
            'socket' => null
        ]);

        $simpleData = explode("\n", $this->data);

        $data = [];
        foreach ($simpleData as $item) {
            if (!$item) {
                continue;
            }
            $full = json_decode($item, true);
            $data[$full['entityType']][] = $item;
        }
        sleep(1);
        foreach ($data as $k => $v) {
            $mapper->execute($k, $v);
        }

        $actualTable   = $this->getTable('imega_groups');
        $expectedTable = $this->getDataSet()->getTable('imega_groups');
        $this->assertTablesEqual($expectedTable, $actualTable);

        $actualTable   = $this->getTable('imega_misc');
        $expectedTable = $this->getDataSet()->getTable('imega_misc');
        $this->assertTablesEqual($expectedTable, $actualTable);

        $actualTable   = $this->getTable('imega_prod');
        $expectedTable = $this->getDataSet()->getTable('imega_prod');
        $this->assertTablesEqual($expectedTable, $actualTable);

        $actualTable   = $this->getTable('imega_prop');
        $expectedTable = $this->getDataSet()->getTable('imega_prop');
        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    private function getTable($name)
    {
        return $this->getConnection()->createQueryTable($name, "SELECT * FROM $name");
    }
}

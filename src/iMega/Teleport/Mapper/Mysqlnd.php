<?php

namespace iMega\Teleport\Mapper;

use iMega\Teleport\Exception\MySQLiException;
use iMega\Teleport\MapperInterface;

/**
 * Class Mapper
 */
class Mysqlnd extends \mysqli implements MapperInterface
{
    protected $prefix;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->open(
            $options['host'],
            $options['db'],
            $options['user'],
            $options['pass'],
            $options['port'],
            $options['socket']
        );
    }

    public function preQuery($data) {}

    /**
     * @param int   $key
     * @param array $data
     */
    public function query($key, array $data)
    {
        $values = $this->getValues($key, $data);
        if ($values) {
            $head = $this->getHead($key);
            $this->execute($head . $values);
        }
    }

    public function postQuery($data)
    {

    }

    private function getHead($key)
    {
        $tablename = $this->prefix . Map::getTables()[$key];
        $fields    = implode(',', Map::getMap()[$key]);
        return "insert into $tablename($fields)values";
    }

    /**
     * @param int   $key
     * @param array $data
     *
     * @return string
     */
    private function getValues($key, array $data)
    {
        $result = '';
        foreach ($data as $item) {
            $record = json_decode($item);
            foreach (Map::getMap()[$key] as $k) {
                $result .= $this->escapeString($record[$k]);
            }
        }

        return $result;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function escapeString($value)
    {
        return $value;
    }

    /**
     * Connect to db
     *
     * @param string $host
     * @param string $db
     * @param string $user
     * @param string $pass
     * @param int|null $port
     * @param string|null $socket
     */
    private function open($host, $db, $user, $pass, $port = null, $socket = null)
    {
        parent::connect($host, $db, $user, $pass, $port = null, $socket = null);
    }

    /**
     * Run Query
     *
     * @param string $statement SQL statement
     * @param int    $type      Result Mode MYSQLI_USE_RESULT or MYSQLI_STORE_RESULT
     *
     * @throws MySQLiException
     * @return \mysqli_result
     */
    private function execute($statement, $type = MYSQLI_USE_RESULT)
    {
        $result = parent::query($statement, $type);
        if (! empty($this->error)) {
            throw new MySQLiException('Error: ' . $this->error . "\r\n\t" . 'Statement: ' . $statement, $this->errno);
        }

        return $result;
    }
}

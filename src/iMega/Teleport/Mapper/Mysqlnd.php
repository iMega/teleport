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
        $this->prefix = $options['prefix'];
        $this->open(
            $options['host'],
            $options['user'],
            $options['password'],
            $options['name'],
            $options['port'],
            $options['socket']
        );
    }

    /**
     * @param string $data Mysql query.
     *
     * @throws MySQLiException
     */
    public function preExecute($data)
    {
        $this->query($data);
    }

    /**
     * @param int   $key
     * @param array $data
     */
    public function execute($key, array $data)
    {
        if (!array_key_exists($key, Map::getTables())) {
            return;
        }
        $values = $this->getValues($key, $data);
        if ($values) {
            $head = $this->getHead($key);
            $this->query($head . $values);
        }
    }

    /**
     * @param string $data Mysql query.
     *
     * @throws MySQLiException
     */
    public function postExecute($data)
    {
        $this->query($data);
    }

    private function getHead($key)
    {
        $tablename = $this->prefix . Map::getTables()[$key];
        $fields    = implode(',', Map::getMap()[$key]);
        return "insert into $tablename($fields)values";
    }

    /**
     * @param int   $key
     * @param array $values
     *
     * @return string
     */
    private function getValues($key, array $values)
    {
        $data = [];
        foreach ($values as $item) {
            $data[] = $this->getValue($key, $item);
        }
        $result = implode(',', $data);

        return $result;
    }

    /**
     * @param int    $key
     * @param string $value JSON.
     *
     * @return string
     */
    private function getValue($key, $value)
    {
        $data = [];
        $record = json_decode($value, true);
        foreach (Map::getMap()[$key] as $field) {
            if (array_key_exists($field, $record)) {
                $res = $record[$field];
            } else {
                $res = '';
            }
            $data[] = $this->escapeString($res);
        }
        $result = implode(',', $data);

        return "($result)";
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function escapeString($value)
    {
        $result = $this::escape_string($value);

        return "'$result'";
    }

    /**
     * Connect to db
     *
     * @param string   $host
     * @param string   $user
     * @param string   $pass
     * @param string   $db
     * @param int|null $port
     * @param string|null $socket
     */
    private function open($host, $user, $pass, $db, $port = null, $socket = null)
    {
        $this::connect($host, $user, $pass, $db, $port = null, $socket = null);
        $this->set_charset('utf8');
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
    public function query($queries)
    {
        $result = $this::multi_query($queries);
        while ($this->more_results() && $this->next_result()) {
            $mysqliResult = $this->use_result();
            if ($mysqliResult instanceof \mysqli_result) {
                $mysqliResult->free();
            }
        }
        if (! empty($this->error)) {
            throw new MySQLiException('Error: ' . $this->error . "\r\n\t" . 'Statement: ', $this->errno);
        }

        return $result;
    }
}

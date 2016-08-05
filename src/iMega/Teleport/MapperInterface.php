<?php

namespace iMega\Teleport;

interface MapperInterface
{
    public function preExecute($data);
    public function execute($key, array $data);

    /**
     * @param $data
     *
     * @throws \iMega\Teleport\Exception\MySQLiException
     * @return \mysqli_result
     */
    public function postExecute($data);
}

<?php

namespace iMega\Teleport;

interface MapperInterface
{
    public function preExecute($data);
    public function execute($key, array $data);
    public function postExecute($data);
}

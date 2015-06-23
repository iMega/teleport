<?php

namespace iMega\Teleport;

interface MapperInterface
{
    public function preQuery($data);
    public function query($key, array $data);
    public function postQuery($data);
}

<?php

namespace iMega\Teleport\Service;

class AcceptFileService
{
    /**
     * @var \iMega\Teleport\Cloud\ApiCloud $client
     */
    protected $cloud;
    protected $storage;
    protected $files;

    public function __construct($cloud, $storage)
    {
        $this->cloud   = $cloud;
        $this->storage = $storage;
    }

    public function downloads($path, array $files)
    {
        foreach ($files as $item) {
            $this->download($path, $item);
        }
    }

    private function download($path, array $item)
    {
        foreach ($item as $file => $hash) {
            $resource = fopen('gaufrette://teleport' . $file, 'w+');
            $this->cloud->download($path.$file, ['sink' => $resource]);
        }
    }
}

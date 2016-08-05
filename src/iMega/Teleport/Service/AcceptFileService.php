<?php

namespace iMega\Teleport\Service;

class AcceptFileService
{
    /**
     * @var \iMega\Teleport\Cloud\ApiCloud $client
     */
    protected $cloud;
    //protected $storage;
    //protected $files;

    public function __construct($cloud, $storage)
    {
        $this->cloud = $cloud;
        //$this->storage = $storage;
    }

    /**
     * Загрузить файлы
     *
     * @param string $uriPath Путь к файлам
     * @param array  $files   Список файлов
     */
    public function downloads($uriPath, array $files)
    {
        foreach ($files as $item) {
            $this->download($uriPath, $item);
        }
        $this->cloud->downloadComplete();
    }

    /**
     * Загрузить файл
     *
     * @param string $uriPath Путь к файлам
     * @param array  $item    Файл с хешем
     */
    private function download($uriPath, array $item)
    {
        foreach ($item as $file => $hash) {
            $resource = fopen('gaufrette://teleport' . $file, 'w+');
            $this->cloud->download($uriPath.$file, ['sink' => $resource]);
            if ($hash == md5_file('gaufrette://teleport' . $file)) {
                $this->cloud->downloadFileComplete(['file' => $file,]);
            }
        }
    }
}

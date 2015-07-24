<?php
/**
 * Copyright (C) 2014 iMega ltd Dmitry Gavriloff (email: info@imega.ru),
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace iMega\Teleport\Subscriber;

use iMega\Teleport\Mapper\Map;
use iMega\Teleport\MapperInterface;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use iMega\Teleport\BufferInterface;
use iMega\Teleport\Events;

/**
 * Class BufferSubscriber
 */
class PackerSubscriber implements EventSubscriberInterface
{
    /**
     * @var array
     */
    protected $len = [];

    /**
     * @var BufferInterface
     */
    protected $buffer;

    /**
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * Max length the pack
     *
     * @var int
     */
    protected $packSize;

    /**
     * @param BufferInterface $buffer
     * @param int             $packSize
     */
    public function __construct(BufferInterface $buffer, MapperInterface $mapper, $packSize)
    {
        $this->buffer   = $buffer;
        $this->mapper   = $mapper;
        $this->packSize = $packSize;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::BUFFER_PARSE_STOCK      => ['packStock', 100],
            Events::BUFFER_PARSE_STOCK_END  => ['packStockEnd', 200],
            Events::BUFFER_PARSE_OFFERS     => ['packOffer', 100],
            Events::BUFFER_PARSE_OFFERS_END => ['packOfferEnd', 200],
        );
    }

    /**
     * Handler event parse stock
     *
     * @param Events\ParseStock $event Data in event.
     */
    public function packStock(Events\ParseStock $event)
    {
        $data = $event->getData();
        $this->addLength($data['entityType'], $data);
        if ($this->packSize <= $this->len[$data['entityType']]) {
            $this->packData($data['entityType']);
        }
    }

    /**
     * Handler event parse stock end
     */
    public function packStockEnd()
    {
        $keys = $this->buffer->keys();
        foreach ($keys as $key => $v) {
            $this->packData($key);
        }
    }

    /**
     * Handler event parse offer
     *
     * @param Events\ParseOffer $event Data in event.
     */
    public function packOffer(Events\ParseOffer $event)
    {
        $data = $event->getData();
        $this->addLength($data['entityType'], $data);
        if ($this->packSize <= $this->len[$data['entityType']]) {
            $this->packData($data['entityType']);
        }
    }

    /**
     * Handler event parse offer end
     */
    public function packOfferEnd()
    {
        $keys = $this->buffer->keys();
        foreach ($keys as $key => $v) {
            $this->packData($key);
        }
    }

    /**
     * Pack data and to send in mapper
     *
     * @param $key
     */
    private function packData($key)
    {
        $records = $this->buffer->get($key);
        $this->mapper->execute($key, $records);
        $this->buffer->clear($key);
    }

    /**
     * Add length string
     *
     * @param int   $key
     * @param array $data
     */
    private function addLength($key, array $data)
    {
        if (array_key_exists($key, $this->len)) {
            $this->len[$key] += mb_strlen($this->json($data));
        } else {
            $this->len[$key] = mb_strlen($this->json($data));
        }
    }

    /**
     * @param array $data The data being encoded.
     *
     * @return string
     */
    private function json(array $data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}

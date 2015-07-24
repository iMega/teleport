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

use Silex\Application;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use iMega\Teleport\BufferInterface;
use iMega\Teleport\Events;

/**
 * Class BufferSubscriber
 */
class BufferSubscriber implements EventSubscriberInterface
{
    /**
     * @var BufferInterface
     */
    private $buffer;

    /**
     * @param BufferInterface $buffer Buffer.
     */
    public function __construct(BufferInterface $buffer)
    {
        $this->buffer = $buffer;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::BUFFER_PARSE_STOCK  => ['parseStock', 200],
            Events::BUFFER_PARSE_OFFERS => ['parseOffers', 200],
        );
    }

    /**
     * @param Events\ParseStock $event
     */
    public function parseStock(Events\ParseStock $event)
    {
        $data = $event->getData();

        $this->buffer->set($data['entityType'], $this->json($data));
    }

    /**
     * @param Events\ParseOffer $event
     */
    public function parseOffers(Events\ParseOffer $event)
    {
        $data = $event->getData();

        $this->buffer->set($data['entityType'], $this->json($data));
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

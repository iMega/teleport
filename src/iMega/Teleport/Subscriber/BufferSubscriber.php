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
use iMega\Teleport\Events\ParseStock;
use iMega\Teleport\BufferInterface;

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
            'buffer.parse.stock' => ['parseStock', 100],
        );
    }

    /**
     * @param ParseStock $event
     */
    public function parseStock(ParseStock $event)
    {
        $data = $event->getData();

        $this->buffer->set($data['entityType'], $data);
    }
}

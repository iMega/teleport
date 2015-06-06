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
use Psr\Log\LoggerInterface;
/**
 * Class LoggerSubscriber
 */
class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger Logger.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            'buffer.parse.stock' => ['ParseStock', 100],
        );
    }

    /**
     * @param ParseStock $event
     */
    public function ParseStock(ParseStock $event)
    {
        $this->logger->debug(get_class($event), $event->getData());
    }
}

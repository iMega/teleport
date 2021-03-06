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

namespace iMega\CMS\Wordpress;

use iMega\Teleport\MapperInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use iMega\Teleport\StorageInterface;
use iMega\Teleport\Events;

class Subscriber implements EventSubscriberInterface
{
    /**
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * @var array
     */
    protected $patterns;

    /**
     * @var StorageInterface
     */
    protected $resources;

    /**
     * @param MapperInterface  $mapper
     * @param StorageInterface $resources
     * @param array            $patterns
     */
    public function __construct(MapperInterface $mapper, $resources, $patterns)
    {
        $this->mapper    = $mapper;
        $this->patterns  = $patterns;
        $this->resources = $resources;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::EXECUTE_DUMP => ['executeDump', 200],
        );
    }

    /**
     * @param Events\DumpEvent $dumpEvent
     */
    public function executeDump(Events\DumpEvent $dumpEvent)
    {
        $filename = $dumpEvent->getData();
        $queries  = str_replace(array_keys($this->patterns), array_values($this->patterns), $this->resources->read($filename));
        $this->mapper->postExecute($queries);
    }
}

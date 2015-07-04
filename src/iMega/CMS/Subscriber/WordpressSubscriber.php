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
namespace iMega\CMS\Subscriber;

use iMega\Teleport\MapperInterface;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use iMega\Teleport\StorageInterface;
use iMega\Teleport\Events;

/**
 * Class WordpressSubscriber
 */
class WordpressSubscriber implements EventSubscriberInterface
{
    /**
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var StorageInterface
     */
    protected $resources;

    /**
     * @param MapperInterface  $mapper
     * @param StorageInterface $resources
     * @param string           $prefix
     */
    public function __construct(MapperInterface $mapper, $resources, $prefix)
    {
        $this->mapper    = $mapper;
        $this->prefix    = $prefix;
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
            Events::BUFFER_PARSE_STOCK_PRE => ['parseStockPre', 200],
        );
    }

    /**
     * Event
     */
    public function parseStockPre()
    {
        $queries = str_replace('{$table_prefix}', $this->prefix, $this->resources->read('tabs.sql'));
        $this->mapper->preExecute($queries);
    }
}

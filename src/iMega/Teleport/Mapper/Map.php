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
namespace iMega\Teleport\Mapper;

use iMega\Teleport\Parser\Offers;
use iMega\Teleport\Parser\Stock;

/**
 * Class Map
 */
final class Map
{
    /**
     * @return array
     */
    public static function getMap()
    {
        return [
            Stock::KEY_GROUPS => [
                'guid',
                'parent',
                'title',
                'slug',
            ],
            Stock::KEY_PROP => [
                'guid',
                'title',
                'slug',
                'val_type',
                'parent_guid',
            ],
            Stock::KEY_PROD => [
                'title',
                'descr',
                'excerpt',
                'guid',
                'slug',
                'catalog_guid',
                'article',
                'img',
                'img_prop',
            ],
            Stock::KEY_MISC => [
                'type',
                'guid',
                'label',
                'val',
                'labelSlug',
                'countAttr',
                'valSlug',
                '_visible',
            ],
            Offers::KEY_OFFERS => [
                'guid',
                'prod_guid',
                'barcode',
                'title',
                'base_unit',
                'base_unit_key',
                'base_unit_title',
                'base_unit_int',
                'amount',
                'postType',
            ],
            Offers::KEY_FEATURES => [
                'offer_guid',
                'prodGuid',
                'variantGuid',
                'title',
                'val',
                'titleSlug',
                'valSlug',
            ],
            Offers::KEY_PRICES => [
                'offer_guid',
                'title',
                'price',
                'currency',
                'unit',
                'ratio',
                'type_guid',
            ],
        ];
    }

    /**
     * @return array
     */
    public static function getTables()
    {
        return [
            Stock::KEY_GROUPS    => 'imega_groups',
            Stock::KEY_PROP      => 'imega_prop',
            Stock::KEY_PROD      => 'imega_prod',
            Stock::KEY_MISC      => 'imega_misc',
            Offers::KEY_OFFERS   => 'imega_offers',
            Offers::KEY_FEATURES => 'imega_offers_features',
            Offers::KEY_PRICES   => 'imega_offers_prices',
        ];
    }
}
